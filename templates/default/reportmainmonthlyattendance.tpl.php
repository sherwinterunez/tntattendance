<?php


//pre($vars);

?>
<style>
body, html {
  overflow: unset;
}
body, html, #printform {
  height: auto;
  width: auto;
}
@media all {
	.page-break	{ display: none; }
}

@media print {
	.page-break	{ display: block; page-break-before: always; }
}
#printform .schoolName_%formval% {
  font-size: 25px;
}
#printform .period_%formval% {
  font-size: 14px;
}
#printform .monthlyattendancereport_%formval% {
  font-size: 18px;
}
#printform .yearlevel_%formval% {
  font-size: 16px;
}
#printform .section_%formval% {
  font-size: 14px;
  font-weight: normal;
}
#printform .studentName_%formval% {
  font-size: 12px;
  font-weight: normal;
}
#printform .studentName_%formval% div.dhxform_txt_label2 {
  overflow: hidden;
  display: block;
  height: 20px;
}
#printform .block_%formval% {
  /*display: block;
  border: 1px solid #00f;*/
}
#printform .absent_%formval% {
  display: block;
  width: 32px;
  height: 1px;
  border-bottom: 20px solid #f00;
  margin-top: -3px;
}
#printform .present_%formval% {
  display: block;
  width: 32px;
  height: 1px;
  border-bottom: 20px solid #ffff0b;
  margin-top: -3px;
}
#printform .block_%formval% div.dhxform_txt_label2 {
  margin: 0;
  padding: 0;
  /*text-align: center;*/
}
#printform .block_%formval% .ddmm_%formval% div.dhxform_txt_label2 {
  margin: 0;
  text-align: center;
}
</style>
<div id="printform"></div>
<script type="text/javascript">
  var myForm = <?php echo $vars['json']; ?>

  var formData = [
    {type: "settings", position: "label-left", labelWidth: 130, inputWidth: 200},
    {type: "block", name: "tbReports", hidden:false, width: 500, blockOffset: 0, offsetTop:0, list:myForm.tbReports},
    {type: "label", label: ""}
  ];

  var pForm = new dhtmlXForm("printform",formData);

  //document.body.style.overflow = unset;
  //document.html.style.overflow = unset;

  window.print();

</script>
