               <div id="kt_app_footer" class="app-footer">
							<!--begin::Footer container-->
							<div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
								<!--begin::Copyright-->
								<div class="text-dark order-2 order-md-1">
									<span class="text-muted fw-semibold me-1">{{ date("Y") }} &copy;</span>
									<a href="https://keenthemes.com" target="_blank" class="text-gray-800 text-hover-primary">
                           {{ config('app.name') }}
                           </a>
								</div>
								<!--end::Copyright-->
							</div>
							<!--end::Footer container-->
						</div>
						<!--end::Footer-->
					</div>
					<!--end:::Main-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>

<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Vendors Javascript(used for this page only)-->
      {!! Html::script('assets2/js/jquery.min.js') !!}
		<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
		<script src="{{ asset('assets/plugins/custom/vis-timeline/vis-timeline.bundle.js') }}"></script>
		<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
		<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
		<script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-campaign.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-project/type.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-project/budget.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-project/settings.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-project/team.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-project/targets.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-project/files.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-project/complete.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-project/main.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>
      <script src="{{ asset('assets2/js/funciones.js') }}"></script>
      <script src="{{ asset('assets2/js/bootbox.min.js') }}"></script>
      <script src="{{ asset('assets2/plugins/notifyjs/dist/notify.min.js') }}"></script>
      <script src="{{ asset('assets2/plugins/notifyjs/dist/styles/bootstrap/notify-bootstrap.js') }}"></script>
      <script src="{{ asset('assets2/plugins/notifications/notify-metro.js') }}"></script>
      {!! Html::script('assets2/plugins/input-mask/jquery.inputmask.js') !!}
      {!! Html::script('assets2/plugins/timepicker/bootstrap-timepicker.min.js') !!}
      {!! Html::script('assets2/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') !!}
      {!! Html::script('assets2/plugins/bootstrap-daterangepicker/daterangepicker.js') !!}
      {!! Html::script('assets2/plugins/input-mask/jquery.inputmask.extensions.js') !!}
      {!! Html::script('assets2/plugins/input-mask/jquery.inputmask.date.extensions.js') !!}
      {!! Html::script('assets2/plugins/input-mask/jquery.inputmask.numeric.extensions.js') !!}
      {!! Html::script('assets2/plugins/input-mask/jquery.inputmask.phone.extensions.js') !!}
      {!! Html::script('assets2/plugins/input-mask/jquery.inputmask.regex.extensions.js') !!}
      <script>
         if (typeof $.Notification === 'undefined') {
            $.Notification = {
               autoHideNotify: function (type, position, message, title) {
                     $.notify(message, {
                        className: type,
                        globalPosition: position || 'top right',
                        autoHideDelay: 3000
                     });
               }
            };
         }

        $(document).ready(function(){
            cargarRuta('{{ route("dashboardmain.index") }}', 'kt_app_content_container');
        });
      </script>