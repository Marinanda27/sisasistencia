<?php
use App\Models\Menuoptioncategory;
use App\Models\Menuoption;
use App\Models\Permission;
use App\Models\User;
use App\Models\Person;
$user = Auth::user();
$person = Person::find($user->person_id);
session(['usertype_id' => $user->usertype_id]);
$tipousuario_id        = session('usertype_id');
?>
						<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
							<!--begin::Menu wrapper-->
							<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
								<!--begin::Scroll wrapper-->
								<div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
									<!--begin::Menu-->
									<div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
										<!--begin:Menu item-->
										  @foreach(\App\Helpers\MenuHelper::generarMenu(auth()->user()->usertype_id) as $categoria)
                                    <x-menu-item :categoria="$categoria" />
                                 @endforeach
									</div>
                           
								</div>
							</div>
							<!--end::Menu wrapper-->
						</div>
						<!--end::sidebar menu-->
					</div>
					<!--end::Sidebar-->
					<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
						<!--begin::Content wrapper-->
						<div class="d-flex flex-column flex-column-fluid">
							<!--begin::Toolbar-->
							<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                        <div id="kt_app_content" class="app-content flex-column-fluid" style="flex:1">
                           <!--begin::Content container-->
                           <div id="kt_app_content_container" class="app-container container-xxl">
                              
                           </div>
                           <!--end::Content container-->
                        </div>
							</div>
						</div>


