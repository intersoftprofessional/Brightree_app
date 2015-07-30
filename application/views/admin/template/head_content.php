<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/shadowbox/shadowbox.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/shadowbox/shadowbox.css" />
<script type="text/javascript">
Shadowbox.init();
</script>


<?php $urls1 = explode('/',$_SERVER['REQUEST_URI']); ?>
<link href="<?php echo base_url(); ?>theme/sos/css/rs-main.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Cuprum' rel='stylesheet' type='text/css' />
<?php if(!in_array('upload_photos',$urls1) && !in_array('upload_profile_photo',$urls1)) {  ?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/flot/jquery.flot.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/flot/jquery.flot.orderBars.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/flot/jquery.flot.pie.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/flot/excanvas.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/flot/jquery.flot.resize.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/tables/colResizable.min.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/forms.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.autosize.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/autotab.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.validationEngine-en.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.validationEngine.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.dualListBox.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.select2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.inputlimiter.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/forms/jquery.wysiwyg.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/other/calendar.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/other/elfinder.min.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/uploader/plupload.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/uploader/plupload.html5.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/uploader/plupload.html4.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/uploader/jquery.plupload.queue.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.progress.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.jgrowl.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.tipsy.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.alerts.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.colorpicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.mousewheel.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/wizards/jquery.form.wizard.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/wizards/jquery.validate.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.breadcrumbs.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.collapsible.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.ToTop.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.listnav.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.sourcerer.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.timeentry.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/plugins/ui/jquery.prettyPhoto.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/custom.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/js/charts/chart.js"></script>

<!--[if gte IE 9]>
  <style type="text/css">
    .leftNav ul li a:hover, .leftNav ul li a.active {
       filter: none;
    }
  </style>
<![endif]-->

