<?php
ini_set('display_errors', false);
ini_set('display_startup_errors', false);
session_start();
$security_test=1;
include("config.php");
include("function.php");

if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
$userid=$_SESSION['userid'];
$menu=$_GET['menu'];
if($menu=='course_qeyd') $ptitle="Tədbirə yazılanlar";
elseif($menu=='course') $ptitle="Kurslar";
elseif($menu=='xeber') $ptitle="Xəbərlər";
elseif($menu=='susers') $ptitle="Sayt istfadəçiləri";
else $ptitle="Admin Panel";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?php print $ptitle;?></title>
        <!-- Favicon -->
        <link rel="shortcut icon" href="/assets/img/logos/logo.png">

        <!-- Bootstrap CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <!-- Font Awesome CSS -->
        <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" type="text/css" href="https://bax.tv/bootstrap/css/bootstrap-datetimepicker.min.css">

        <!-- Custom CSS -->
        <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>

        <!-- BEGIN CSS for this page -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css"/>
        <!-- END CSS for this page --> 
        <script>
            function open_popup(url)
            {
                var w = 880;
                var h = 570;
                var l = Math.floor((screen.width-w)/2);
                var t = Math.floor((screen.height-h)/2);
                var win = window.open(url, 'ResponsiveFilemanager', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
            }             
            function Pencere(adres){
                window.open(adres,"yeniPen","height=650, width=900,top=10,left=10,scrollbars=yes,toolbar=no,menubar=no,location=no,resizable=no");
            }

            function logout() {
                if (confirm("Are you want logout?"))
                    document.location.href="logout.php";
                return false;
            }

            function Del(url) {
                if (confirm("Are you shure to delete?"))
                    document.location.href=url;
                return false;
            }

            function Change(url) {
                if (confirm("Are you shure to Change?"))
                    document.location.href=url;
                return false;
            }

            function Deluser(id) {
                if (confirm("Are you shure to delete?"))
                    document.location.href="?menu=qeydiyyat&tip=delete_qeydiyyat&cid="+id;
                return false;
            }
        </script>
    </head>

    <body class="adminbody">

        <div id="main" style="background-color: #efefef;">

            <!-- top bar navigation -->
            <div class="headerbar">

                <!-- LOGO -->
                <div class="headerbar-left">
                    <a href="index.php" class="logo"><img src="/assets/img/logos/logo.png" /> <span></span></a>
                </div>

                <nav class="navbar-custom">

                    <ul class="list-inline float-right mb-0">

                        <!--                        <li class="list-inline-item dropdown notif">
                        <a class="nav-link dropdown-toggle arrow-none" href="#">
                        <i class="fa fa-fw fa-exclamation-circle"></i>
                        </a>
                        </li> -->


                        <li class="list-inline-item dropdown notif">
                            <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="assets/images/avatars/admin.png" alt="Profile image" class="avatar-rounded">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5 class="text-overflow"><small>Hello, <?php print $_SESSION['username'];?></small> </h5>
                                </div>

                                <!-- item-->
                                <a href="#pro-profile.html" class="dropdown-item notify-item">
                                    <i class="fa fa-user"></i> <span>Profile</span>
                                </a>

                                <!-- item-->
                                <a onclick="logout();" href="#" class="dropdown-item notify-item">
                                    <i class="fa fa-power-off"></i> <span>Logout</span>
                                </a>

                            </div>
                        </li>

                    </ul>

                    <ul class="list-inline menu-left mb-0">
                        <li class="float-left" style="padding-left: 10px;">
                            <button class="button-menu-mobile open-left">
                                <i class="fa fa-fw fa-bars"></i>
                            </button>
                        </li>                        
                    </ul>

                </nav>

            </div>
            <!-- End Navigation -->


            <!-- Left Sidebar -->
            <div class="left main-sidebar">

                <div class="sidebar-inner leftscroll">

                    <div id="sidebar-menu">
                        <ul>
                            <?php include "menu.php";?>



                        </ul>

                        <div class="clearfix"></div>

                    </div>

                    <div class="clearfix"></div>

                </div>

            </div>
            <!-- End Sidebar -->


            <div class="content-page">

                <!-- Start content -->
                <div class="content">

                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="breadcrumb-holder">
                                    <h1 class="main-title float-left">Dashboard</h1>
                                    <ol class="breadcrumb float-right">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->


                        <?php
                        if (empty($_GET['menu'])){
                            echo "<p align='center'><br><br><b>Admin Panelə xoş gəldiniz.</b><br><br>";
                            echo "Son Giriş tarixi <strong>".$_SESSION['last_login']."</strong>, Son Giriş IP-si <strong>".$_SESSION['last_ip']."</strong><br>";
                            echo "<b>İşləmək üçün hər hansı bir bölməni seçin.</b></p>\n"; 
                            //include("main.php");
                        }
                        else {
                            $pages = array('menyu','fmanager', 'projects', 'ourteam', 'users','services','services_industry','services_service','services_solution','susers', 'xeber','company','sifaris','files', 'stext', 'abune','news_photos', 'banner', 'content', 'qeydiyyat', 'multimedia', 'photos','katalog', 'edit_user', 'links', 'bloks', 'banner_player');
                            if( in_array($_GET['menu'], $pages) )
                                include($_GET['menu'].".php");
                            else
                                die("<script>document.location.href='index.php';</script>");
                        }
                        ?>           



                    </div>
                    <!-- END container-fluid -->

                </div>
                <!-- END content -->

            </div>
            <!-- END content-page -->

            <footer class="footer">
                <span class="text-right">
                    Copyright <a target="_blank" href="/"><?php print strtoupper($siteName);?></a>
                </span>
                <span class="float-right">
                    Powered by <a target="_blank" href="https://mahmudlu.az/"><b>MTIO</b></a>
                </span>
            </footer>

        </div>
        <!-- END main -->

        <script src="assets/js/modernizr.min.js"></script>
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/jquery-ui.min.js"></script>
        <script src="assets/js/moment.min.js"></script>

        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>

        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>

        <!-- App js -->
        <script src="assets/js/pikeadmin.js"></script>

        <!-- BEGIN Java Script for this page -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>

        <!-- Counter-Up-->
        <script src="https://bax.tv/bootstrap/js/bootstrap-datetimepicker.min.js"></script> 
        <script src="https://bax.tv/bootstrap/js/bootstrap-datetimepicker.az.js"></script> 
        <script src="assets/plugins/waypoints/lib/jquery.waypoints.min.js"></script>
        <script src="assets/plugins/counterup/jquery.counterup.min.js"></script>            
        <script type="text/javascript" src="teditor/tinymce.min.js"></script>
        <script src="assets/plugins/select2/js/select2.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function(){     
                function slideout(){
                    setTimeout(function(){
                        $("#response").slideUp("slow", function () {  }); }, 2000);
                }
                $("#response").hide();
                $(function() {
                    $("#listMenyu ul").sortable({ opacity: 0.8, cursor: 'move', update: function() {
                        var order = $(this).sortable("serialize") + '&update=update'; 
                        $.post("updatemenyu.php", order, function(theResponse){
                            $("#response").html(theResponse);
                            $("#response").slideDown('slow');
                            slideout();
                        });                                                              
                        }                                  
                    });
                });

                $('.select21').select2();
                $('.select22').select2();
                $('.select23').select2();
                $('.select24').select2();
                $('.select25').select2();
                $('.select26').select2();
                $('.select27').select2();


                function slideout(){
                    setTimeout(function(){
                        $("#response").slideUp("slow", function () {  }); }, 2000);
                }
                $(function() {
/*                    $("#listKatalog tbody").sortable({ opacity: 0.8, cursor: 'move', update: function() {
                        var order = $(this).sortable("serialize") + '&update=update'; 
                        $.post("updatenews.php", order, function(theResponse){
                            $("#response").html(theResponse);
                            $("#response").slideDown('slow');
                            slideout();
                        });                                                              
                        }                                  
                    }); */
                    $("#listTeam tbody").sortable({ opacity: 0.8, cursor: 'move', update: function() {
                        var order = $(this).sortable("serialize") + '&update=update'; 
                        $.post("updateourteam.php", order, function(theResponse){
                            $("#response").html(theResponse);
                            $("#response").slideDown('slow');
                            slideout();
                        });                                                              
                        }                                  
                    });
                });

            });    
        </script>        
        <script type="text/javascript">
            tinymce.init({
                selector: "textarea",theme: "modern",width: '100%',height: 250,
                selector : "textarea:not(.mceNoEditor)",
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                    "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
                ],
                toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
                toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
                image_advtab: true ,
                relative_urls: false,
                verify_html : false,
                content_css: [
                            '/assets/css/framework/bootstrap-reboot.min.css',
                            '/assets/css/framework/bootstrap-grid.min.css',
                            '/assets/css/framework/bootstrap-utilities.min.css',
                            '/assets/fonts/Socicons/socicon.css',
                            '/assets/css/ptf-main.min.css',
                            '/assets/css/custom.css'
                ],
                external_filemanager_path:"/mpan/filemanager/",
                filemanager_access_key: "<?php print $_SESSION['userAccessKey'];?>",
                filemanager_title:"Filemanager" ,
                external_plugins: { "filemanager" : "/mpan/filemanager/plugin.min.js"}
            });
        </script>

        <script>
            function changeEditor(textarea_id) {
                tinyMCE.EditorManager.execCommand('mceToggleEditor', true, textarea_id);
                /*                $("a.toggle").toggle(function(){
                tinyMCE.execCommand('mceRemoveControl', false, id);
                }, function () {
                tinyMCE.execCommand('mceAddControl', false, id);
                });*/
            }


            function toggle_tinymce_checkbutton(checkButtonId,strItemId){
                var toggle = $('#'+checkButtonId);  // checkButtonId = id of checkbutton w/o #
                if(toggle.attr('value') == 'on') {
                    var editor = tinymce.EditorManager.get(strItemId); // strItemId = id of textarea w/o #
                    editor.remove();
                    toggle.attr('value','off');
                } else {
                    var editor = tinymce.EditorManager.createEditor(strItemId,{
                        selector: "textarea",theme: "modern",width: '100%',height: 250,
                        selector : "textarea:not(.mceNoEditor)",
                        plugins: [
                            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                            "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
                        ],
                        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
                        toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
                        image_advtab: true ,
                        relative_urls: false,
                        verify_html : false,
                        content_css: [
                            '/assets/css/framework/bootstrap-reboot.min.css',
                            '/assets/css/framework/bootstrap-grid.min.css',
                            '/assets/css/framework/bootstrap-utilities.min.css',
                            '/assets/fonts/Socicons/socicon.css',
                            '/assets/css/ptf-main.min.css',
                            '/assets/css/custom.css'
                        ],
                        external_filemanager_path:"/mpan/filemanager/",
                        filemanager_access_key: "<?php print $_SESSION['userAccessKey'];?>",
                        filemanager_title:"Filemanager" ,
                        external_plugins: { "filemanager" : "/mpan/filemanager/plugin.min.js"}
                    });
                    editor.render();
                    toggle.attr('value','on');}
            }            


            $(document).ready(function() {

                $("#schedule_date").datetimepicker({
                    format: 'yyyy-mm-dd hh:ii:ss',
                    todayBtn: true,  
                    autoclose: true,
                    language:'az'
                }); 

                // data-tables
                $('#example1').DataTable();

                // counter-up
                $('.counter').counterUp({
                    delay: 10,
                    time: 600
                });


                $("#model_select").children('option:gt(0)').hide();
                $("#brand_select").change(function() {
                    $("#model_select").children('option').hide();
                    //$("#model_select").children("option[id=" + $(this).val() + "]").show()
                    $("#model_select").children("option[id=" + $(this).find("option:selected").attr("id") + "]").show()
                });

                $('#listBorc').DataTable({
                    "lengthMenu": [ 20, 50, 100, 150 ],
                    "processing": true,
                    "serverSide": true,
                    "ordering": false,
                    "ajax": "borclar_datatable.php?type=<?php print $type;?>",
                    "fnDrawCallback": function( oSettings ) {$("[rel='tooltip']").tooltip({placement: 'bottom'});},
                    "language": {"url": "Azerbaijan.json"}
                });

                $('#UsersTable tfoot th').each( function () {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                } );


                $('#listQeyd').DataTable( {
                    "lengthMenu": [ 20, 50, 100, 150 ],
                    "processing": true,
                    "ordering": true,
                    "fnDrawCallback": function( oSettings ) {$("[rel='tooltip']").tooltip({placement: 'bottom'});},
                    "language": {"url": "Azerbaijan.json"},
                    dom: 'Bfrtip',
                    buttons: [
                        'print'
                    ],
                    initComplete: function () {
                        this.api().columns([1,2,3,4]).every( function () {
                            var column = this;
                            var select = $('<select><option value=""></option></select>')
                            .appendTo( $(column.footer()).empty() )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
                                column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                            } );

                            column.data().unique().sort().each( function ( d, j ) {
                                if(column.search() === '^'+d+'$'){
                                    select.append( '<option value="'+d+'" selected="selected">'+d+'</option>' )
                                } else {
                                    select.append( '<option value="'+d+'">'+d+'</option>' )
                                }
                            } );
                        } );
                    }
                } );

                $('#UsersTable').DataTable( {
                    "lengthMenu": [ 20, 50, 100, 150 ],
                    "processing": true,
                    "serverSide": true,
                    "ordering": false,
                    "ajax": "susers_datatable.php",
                    "fnDrawCallback": function( oSettings ) {$("[rel='tooltip']").tooltip({placement: 'bottom'});},
                    "language": {"url": "Azerbaijan.json"}
                } );

                $('#ReportTable').DataTable( {
                    "lengthMenu": [ 20, 50, 100, 150 ],
                    "processing": true,
                    "serverSide": true,
                    "ordering": false,
                    "ajax": "video_report_datatable.php",
                    "fnDrawCallback": function( oSettings ) {$("[rel='tooltip']").tooltip({placement: 'bottom'});},
                    "language": {"url": "Azerbaijan.json"}
                } ); 

                $('#listPlayer tfoot th').each( function () {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                } );                

                $('#listPlayer').DataTable( {
                    "lengthMenu": [ 20, 50, 100, 150 ],
                    "processing": true,
                    "serverSide": true,
                    "ordering": false,
                    "ajax": "vimeo_datatable.php?category_id=<?php print $category_id;?>",
                    "fnDrawCallback": function( oSettings ) {$("[rel='tooltip']").tooltip({placement: 'bottom'});},
                    "language": {"url": "Azerbaijan.json"}

                } );

                $('#listReklam').DataTable( {
                    "lengthMenu": [ 20, 50, 100, 150 ],
                    "processing": true,
                    "serverSide": true,
                    "ordering": false,
                    "ajax": "reklam_datatable.php?category_id=<?php print $category_id;?>",
                    "fnDrawCallback": function( oSettings ) {$("[rel='tooltip']").tooltip({placement: 'bottom'});},
                    "language": {"url": "Azerbaijan.json"}

                } );

                $('#listSifaris').DataTable( {
                    dom: 'Bfrtip',
                    "ordering": false,                    
                    buttons: [
                        'csv', 'excel', 'pdf', 'print'
                    ]
                } );


                /*$('#vupload').load(function() {
                $(this).contents().find('#vimeo').submit(function() { 
                $("#pageloader").fadeIn();
                $("#vimeo_add").fadeOut();
                return true;
                });
                });*/


                $("#vmButton").click(function(){
                    $("#pageloader").fadeIn();
                    $("#vimeo_add").fadeOut();
                    return true;
                });

                $("#ytUpButton").click(function(){
                    $('#vupload').attr('src',$('#youtubeUpload').val());
                    $("#pageloader").fadeIn();
                    $("#vimeo_add").fadeOut();
                    return true;
                });

                $("[rel='tooltip']").tooltip({placement: 'bottom'}); 



                $("#select_industry").children('option:gt(0)').hide();
                $("#select_service").change(function() {
                    $("#select_solution").val(0);
                    $("#select_industry").val(0);
                    $("#select_industry").children('option:gt(0)').hide();
                    $("#select_industry").children("option[id=" + $(this).val() + "]").show()
                });    

                $("#select_solution").children('option:gt(0)').hide();
                $("#select_industry").change(function() {
                    $("#select_solution").val(0);
                    $("#select_solution").children('option:gt(0)').hide();
                    $("#select_solution").children("option[id=" + $(this).val() + "]").show()
                });                

                /*
                $('.fancybox').fancybox({
                width    : 600,
                height: 480,
                openEffect : 'none',
                closeEffect : 'none',
                prevEffect : 'none',
                nextEffect : 'none',
                arrows : false,
                helpers : {
                media : {},
                buttons : {}
                }
                }); */

            }); 

            $(document).ready(function(){     
                function slideout(){
                    setTimeout(function(){
                        $("#response").slideUp("slow", function () {  }); }, 2000);
                }
                $("#response").hide();
                /*                $(function() {
                $("#listMenyu ul").sortable({ opacity: 0.8, cursor: 'move', update: function() {
                var order = $(this).sortable("serialize") + '&update=update'; 
                $.post("updatemenyu.php", order, function(theResponse){
                $("#response").html(theResponse);
                $("#response").slideDown('slow');
                slideout();
                });                                                              
                }                                  
                });
                });*/

            }); 
        </script>

        <script>
            var url = window.location;
            //console.log(url);
            var element = $('ul a').filter(function() {
                //console.log(url);
                return this.href == url || url.href.indexOf(this.href) == 0;
            }).addClass('active').parent().parent().addClass('in').parent();

            if (element.is('a')) {
                element.addClass('active');
            }
        </script>
        <!-- END Java Script for this page -->

    </body>
</html>