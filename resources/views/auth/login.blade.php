@include('auth.header')
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<style>
				body { background-image: linear-gradient(120deg, rgba(13, 25, 48, .86), rgba(18, 63, 91, .62)), url('{{ asset('assets/media/auth/bg10.jpeg') }}'); }
				[data-bs-theme="dark"] body { background-image: linear-gradient(120deg, rgba(13, 25, 48, .9), rgba(18, 63, 91, .74)), url('{{ asset('assets/media/auth/bg10-dark.jpeg') }}'); }
				#kt_app_root { min-height: 100vh; }
				.login-intro { color: #fff; max-width: 560px; }
				.login-intro-icon { width: 72px; height: 72px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(255, 255, 255, .35); border-radius: 22px; background: rgba(255, 255, 255, .12); }
				.login-intro-icon svg { width: 38px; height: 38px; }
				.login-intro h1 { color: #fff; letter-spacing: .04em; }
				.login-card { box-shadow: 0 24px 70px rgba(8, 20, 38, .2); }
				.login-card .form-control { min-height: 52px; border-radius: 10px; }
				.login-card .btn { min-height: 50px; border-radius: 10px; }
				.login-errors { border-radius: 10px; padding: 12px 16px; margin-bottom: 24px; }
				.login-errors ul { margin: 0; padding-left: 20px; }
				@media (max-width: 991.98px) {
					.login-intro { padding-top: 48px !important; padding-bottom: 20px !important; }
					.login-intro-icon { width: 56px; height: 56px; border-radius: 17px; }
					.login-intro-icon svg { width: 30px; height: 30px; }
				}
			</style>
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<div class="d-flex flex-lg-row-fluid">
					<div class="login-intro d-flex flex-column flex-center p-10 p-lg-15 w-100">
						<div class="login-intro-icon mb-8" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="3.5" y="5" width="17" height="16" rx="3" stroke="currentColor" stroke-width="1.7"/>
								<path d="M7.5 3.5V7M16.5 3.5V7M3.5 9.5H20.5M8 14L10.5 16.5L16 12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</div>
						<h1 class="fs-2qx fw-bolder text-center mb-5">SISTEMA DE ASISTENCIAS</h1>
						<p class="fs-4 text-white text-center opacity-75 mb-0">Control y gestión de asistencia en un solo lugar.</p>
					</div>
				</div>
				<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
					<div class="login-card bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10">
						<div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
							<div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
								<form class="form w-100" method="POST" action="{{ route('in') }}">
									@csrf
									<div class="text-center mb-11">
										<h2 class="text-dark fw-bolder mb-3">Iniciar sesión</h2>
										<div class="text-gray-500 fw-semibold fs-6">Ingresa tus datos para continuar</div>
									</div>
                           @if ($errors->any())
                              <div class="alert alert-danger login-errors" role="alert">
                                 <ul>
                                       @foreach ($errors->all() as $error)
                                          <li>{{ $error }}</li>
                                       @endforeach
                                 </ul>
                              </div>
                           @endif
									<div class="fv-row mb-8">
                           	<label class="form-label fw-semibold text-gray-700" for="login">Usuario</label>
                           	<input id="login" type="text" placeholder="Ingresa tu usuario" name="login" autocomplete="username" required class="form-control bg-transparent" />
                           </div>
                           <div class="fv-row mb-8">
                           	<label class="form-label fw-semibold text-gray-700" for="password">Contraseña</label>
                           	<input id="password" type="password" placeholder="Ingresa tu contraseña" name="password" autocomplete="current-password" required class="form-control bg-transparent" />
                           </div>
                           <div class="d-grid mb-10">
                           	<button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
											<span class="indicator-label">Iniciar Sesión</span>
											<span class="indicator-progress">Por favor espera...
											<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@include('auth.footer')