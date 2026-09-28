<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
        data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">{{ $title }}</h3>
        </div>
    </div>
    <div id="kt_account_settings_profile_details" class="collapse show">
        <div class="p-4">
            <div id="divMensajeError{!! $entidad !!}"></div>
        </div>
        {!! Form::model($user, $formData) !!}
        {!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

        <div class="card-body border-top p-9">
            <div class="row mb-6">
                {!! Form::label('photo', 'Foto:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
                <div class="col-lg-8">
                    <div class="image-input image-input-outline" data-kt-image-input="true"
                        style="background-image: url('assets/media/svg/avatars/blank.svg')">
                        <div class="image-input-wrapper w-125px h-125px"
                            style="background-image: url(assets/media/avatars/300-1.jpg)"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Cambiar foto">
                            <i class="ki-duotone ki-pencil fs-7">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="file" name="avatar" accept=".png" />
                            <input type="hidden" name="avatar_remove" />
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div class="form-text text-dark">Tipos de archivo permitidos: png.</div>
                </div>
            </div>
            <div class="row mb-6">
                {!! Form::label('fullname', 'Nombre y Apellidos:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
                <div class="col-lg-8 fv-row">
                    {!! Form::text('fullname',$personname, array('class' => 'form-control form-control-lg form-control-solid text-uppercase text-dark','readonly')) !!}
                </div>
            </div>
            <div class="row mb-6">
                {!! Form::label('document_number', 'Nº de Documento:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
                <div class="col-lg-8 fv-row">
                    {!! Form::text('document_number', $nrodocument, array('class' => 'form-control form-control-lg form-control-solid text-dark','readonly')) !!}
                </div>
            </div>

            <div class="row mb-6">
                {!! Form::label('birthdate', 'Fecha de nacimiento:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
                <div class="col-lg-8 fv-row">
                    {!! Form::text('birthdate', $birhdate, array('class' => 'form-control form-control-lg form-control-solid text-dark','readonly')) !!}
                </div>
            </div>

            <div class="row mb-6">
                {!! Form::label('cellnumber', 'Celular:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
                <div class="col-lg-8 fv-row">
                    {!! Form::text('cellnumber', $cellnumber, array('class' => 'form-control form-control-lg form-control-solid text-dark')) !!}
                </div>
            </div>

            <div class="row mb-6">
                {!! Form::label('address', 'Dirección:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
                <div class="col-lg-8 fv-row">
                    {!! Form::text('address', $address, array('class' => 'form-control form-control-lg form-control-solid text-uppercase text-dark')) !!}
                </div>
            </div>

            <div class="row mb-6">
                {!! Form::label('email', 'Correo electrónico:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
                <div class="col-lg-8 fv-row">
                    {!! Form::email('email', null, array('class' => 'form-control form-control-lg form-control-solid')) !!}
                </div>
            </div>

            <div class="border-top mb-6"></div>
                <div class="row mb-6">
                    {!! Form::label('username', 'Nombre de usuario:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
                    <div class="col-lg-8 fv-row">
                        {!! Form::text('username', $username, array('class' => 'form-control form-control-lg form-control-solid text-dark','readonly')) !!}
                    </div>
                </div>

                <div class="row mb-1">
                    <div class="col-lg-4">
                        <div class="fv-row mb-0">
                            {!! Form::label('currentpassword', 'Contraseña actual:', array('class' => 'form-label fs-6 fw-bold mb-3')) !!}
                            {!! Form::password('currentpassword', array('class' => 'form-control form-control-lg form-control-solid text-dark')) !!}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="fv-row mb-0">
                            {!! Form::label('newpassword', 'Nueva contraseña:', array('class' => 'form-label fs-6 fw-bold mb-3')) !!}
                            {!! Form::password('newpassword', array('class' => 'form-control form-control-lg form-control-solid text-dark')) !!}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="fv-row mb-0">
                            {!! Form::label('confirmpassword', 'Confirmar contraseña:', array('class' => 'form-label fs-6 fw-bold mb-3')) !!}
                            {!! Form::password('confirmpassword', array('class' => 'form-control form-control-lg form-control-solid text-dark')) !!}
                        </div>
                    </div>
                </div>
            
        </div>
        <div class="card-footer d-flex justify-content-end py-6 px-9">
                {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-primary px-8', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
            </div>
        {!! Form::close() !!}
    </div>
</div>

