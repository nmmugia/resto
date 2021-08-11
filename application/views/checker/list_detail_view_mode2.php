<style>
  .kitchen-table td{
    padding: 3px;
    text-transform: uppercase;
    font-weight: bold;
  }
  .kitchen-table th{
    padding: 0px;
  }
  .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus {
    background-color: #881817;
    border-color: #881817;
  }
  .pagination > li > a, .pagination > li > span {
    color: #881817;
  }
  .pagination > li > a:hover, .pagination > li > span:hover, .pagination > li > a:focus, .pagination > li > span:focus{
    color: #881817;
  }
</style>
<div class="row">
  <div class="col-md-8" style="padding: 0px;" id="regular_content">
  <?php echo $checker_left; ?>
  </div>
  <div class="col-md-4" style="padding: 0px;" id="additional_content">
  <?php echo $checker_right; ?>
  </div>
</div>