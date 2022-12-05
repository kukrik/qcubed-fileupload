<?php require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<style>
   body {font-size: 14px;}
    /*p, */footer {font-size: medium;}
    footer {margin-top: 35px;}
    footer span {color: #ffffff;}
   .preview img {
       height: 70px;
       width: 70px;
       object-fit: cover;
       object-position: 100% 0;
   }

</style>

<?php $this->RenderBegin(); ?>

<div class="instructions">
    <h1 class="instruction_title" style="padding-bottom: 15px;">A simple file manager</h1>
    <p>Here we try to show how to easily delete and rename files.
        From here you can do more interesting things or do them in a new way.</p>
</div>
<div class="container">
    <div class="row"  style="padding-top: 30px; padding-bottom: 10px; text-align: right">
        <div class="col-lg-1">
            <?= _r($this->lstItemsPerPage); ?>
        </div>
        <div class="col-lg-3">
            <?= _r($this->txtFilter); ?>
        </div>
        <div class="col-lg-8">
            <?= _r($this->dtgFiles->Paginator); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?= _r($this->dtgFiles); ?>
        </div>
    </div>
</div>

<?php $this->RenderEnd(); ?>

<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>