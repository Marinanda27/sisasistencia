<?php
namespace App\Librerias;

use Illuminate\Support\Facades\DB;

/**
 * Cálculo de la hora límite de puntualidad según el grado registrado en la asistencia.
 * Cada grado puede tener su propio horario (schedule.grade); si no tiene,
 * se usa el horario general (grade NULL) o, en su defecto, el primero registrado.
 */
class HorarioAsistencia
{
    /** Horario general de respaldo. */
    public static function horarioGeneral($base)
    {
        return DB::connection($base)->selectOne(
            "SELECT start_time, grace_minutes FROM schedule
             WHERE deleted_at IS NULL
             ORDER BY grade IS NULL DESC, id ASC LIMIT 1"
        );
    }

    /** Hora límite (H:i:s) de un horario, o null. */
    public static function horaLimite($horario)
    {
        return $horario
            ? date('H:i:s', strtotime($horario->start_time) + ($horario->grace_minutes * 60))
            : null;
    }

    /**
     * JOIN con el horario del grado registrado en la asistencia.
     * @param string $aliasAsistencia alias de la tabla assistance en la consulta
     */
    public static function joinSql($aliasAsistencia)
    {
        return " LEFT JOIN schedule sg ON sg.grade = {$aliasAsistencia}.grade AND sg.deleted_at IS NULL ";
    }

    /**
     * Expresión SQL de la hora límite por fila (requiere joinSql()).
     * $horaLimiteGeneral proviene de date('H:i:s'), por lo que es seguro incrustarlo.
     */
    public static function limiteSql($horaLimiteGeneral)
    {
        $general = $horaLimiteGeneral && preg_match('/^\d{2}:\d{2}:\d{2}$/', $horaLimiteGeneral)
            ? "CAST('" . $horaLimiteGeneral . "' AS TIME)"
            : "NULL";
        return "COALESCE(ADDTIME(sg.start_time, SEC_TO_TIME(sg.grace_minutes * 60)), " . $general . ")";
    }

    /** Expresión SQL del estado: 'Puntual', 'Tardanza' o 'Sin horario'. */
    public static function estadoSql($aliasAsistencia, $horaLimiteGeneral)
    {
        $limite = self::limiteSql($horaLimiteGeneral);
        return "CASE WHEN {$limite} IS NULL THEN 'Sin horario'
                     WHEN TIME({$aliasAsistencia}.dateregister) <= {$limite} THEN 'Puntual'
                     ELSE 'Tardanza' END";
    }

    /** Condición WHERE para filtrar puntual/tardanza ('' si no aplica). */
    public static function filtroSql($aliasAsistencia, $horaLimiteGeneral, $filtro)
    {
        $limite = self::limiteSql($horaLimiteGeneral);
        if ($filtro === 'puntual') {
            return " AND TIME({$aliasAsistencia}.dateregister) <= {$limite}";
        }
        if ($filtro === 'tardanza') {
            return " AND TIME({$aliasAsistencia}.dateregister) > {$limite}";
        }
        return '';
    }
}
