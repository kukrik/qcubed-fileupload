<?php require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<style>
   body {font-size: 14px;}
    /*p, */footer {font-size: medium;}
    footer {margin-top: 35px;}
</style>

<?php $this->RenderBegin(); ?>

<div class="instructions">
    <h1 class="instruction_title" style="padding-bottom: 15px;">QCubed v4 plugin created for FileUpload</h1>
    <p>Many third-party plugins are difficult or inconvenient to integrate with QCubed-4. In this case, we decided to build
        the Fileupload plugin to fit the QCubed-4 framework as much as possible. Here we use JavaScript and a bit of jQuery.
        This plugin works starting from PHP version 8.3+.
    </p>
    <p>See FileUploadBase and UploadHandler class for usage and configuration descriptions and use cases.</p>
    <h3>Requirements</h3>
    <p>First, you must check whether the conditions are met:</p>
    <ol>
        <li>Does the "upload" directory exist in /project/assets.</li>
        <li>Does the "tmp" directory exist in /project.</li>
        <li>Please check if the constants 'APP_UPLOADS_URL', 'APP_UPLOADS_DIR', 'APP_UPLOADS_TEMP_URL', 'APP_UPLOADS_TEMP_DIR' exist in
            <a href="https://github.com/qcubed-4/application/blob/master/install/project/includes/configuration/active/2directories.cfg.php#L29" target="_blank">
                https://github.com/qcubed-4/application/blob/master/install/project/includes/configuration/active/2directories.cfg.php#L29</a></li>
    </ol>

    <p>TThis is just a quick example. The language translations are meant for enabling JavaScript language translation.
        Currently, English, Estonian, and Russian languages are available. The default language is English.</p>
    <p>First, you should know that this plugin is designed with a specific purpose: if you want to create a new folder
        in the "upload" directory, you must also create a folder with the same name in the existing
        "thumbnail", "medium", and "large" folders at exactly the same depth.</p>
    <p>This is a simple and vague example. This is to illustrate how files are uploaded to other folders within the upload directory.
        For example, create a new folder named "test" and create the same folder in the "upload", "thumbnail", "medium",
        and "large" directories. Enter "test" into the text box and upload some files. The files will be uploaded into
        the folders "upload/test", "thumbnail/test", "medium/test", and "large/test", etc.</p>
    <p>Note: This example uses $_SESSION. Please do not use this session method in production. In development and production,
        you should use either $_GET or $_POST data, which are passed to the options['DestinationPath'].</p>

    <p>The FileUpload class handles only file uploading and does not deal with directly deleting files after uploading.
        Before upload, you can review and remove some of the uploaded files. For example, the file manager
        <a href="https://github.com/kukrik/qcubed-filemanager">(https://github.com/kukrik/qcubed-filemanager)</a> is suitable for
        managing files and creating folders.</p>
    <hr>
</div>
<div class="container">
    <div class="col-lg-12" style="padding: 15px 0 15px 0; margin: 0 -15px">
        <div class="col-lg-6 pull-left">
            <?= _r($this->btnEn); ?>
            <?= _r($this->btnEt); ?>
            <?= _r($this->btnRu); ?>
        </div>
        <div class="col-lg-6 pull-right">
            <?= _r($this->txtPathText); ?>
        </div>
    </div>
    <h3>Example Two</h3>
    <div class="row fileupload-buttonbar">
        <div class="col-lg-12">
            <?= _r($this->btnAddFiles); ?>
            <?= _r($this->btnAllStart); ?>
            <?= _r($this->btnAllCancel); ?>
        </div>
    </div>
    <?= _r($this->objUpload); ?>
    <div class="row">
        <div class="col-lg-12">
            <div id="alert-wrapper"></div>
            <div class="alert-multi-wrapper"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="files"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <hr>
            <h3>Options</h3>
            <p>Note: If you want to override some properties, the same properties must be overridden to the same value
                in FileUpload and Uploadhandler.</p>
            <p>The rest of the properties can be set as needed.</p>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Property</th>
                    <th>FileUpload</th>
                    <th>UploadHandler</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>RootPath</td>
                    <td>APP_UPLOADS_DIR</td>
                    <td>APP_UPLOADS_DIR</td>
                </tr>
                <tr>
                    <td>RootUrl</td>
                    <td>APP_UPLOADS_URL</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>TempPath</td>
                    <td>APP_UPLOADS_TEMP_DIR</td>
                    <td>APP_UPLOADS_TEMP_DIR</td>
                </tr>
                <tr>
                    <td>TempUrl</td>
                    <td>APP_UPLOADS_TEMP_URL</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>StoragePath</td>
                    <td>_files</td>
                    <td>_files</td>
                </tr>
                <tr>
                    <td>FullStoragePath</td>
                    <td>null</td>
                    <td>null</td>
                </tr>
                <tr>
                    <td>Language (en, et, ru)</td>
                    <td>en</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>ShowIcons</td>
                    <td>false</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>AcceptFileTypes</td>
                    <td>null</td>
                    <td>null</td>
                </tr>
                <tr>
                    <td>MaxNumberOfFiles</td>
                    <td>null</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>MaxFileSize</td>
                    <td>null</td>
                    <td>null</td>
                </tr>
                <tr>
                    <td>MinFileSize</td>
                    <td>null</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>ChunkUpload</td>
                    <td>true</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>MaxChunkSize</td>
                    <td>5 MB</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>LimitConcurrentUploads</td>
                    <td>2</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Url</td>
                    <td>null</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>PreviewMaxWidth</td>
                    <td>80</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>PreviewMaxHeight</td>
                    <td>80</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>WithCredentials</td>
                    <td>false</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>ImageResizeQuality</td>
                    <td>-</td>
                    <td>85</td>
                </tr>
                <tr>
                    <td>PngLevel</td>
                    <td>-</td>
                    <td>6</td>
                </tr>
                <tr>
                    <td>ImageResizeFunction (imagecopyresampled or imagecopyresized)</td>
                    <td>-</td>
                    <td>imagecopyresampled</td>
                </tr>
                <tr>
                    <td>ImageResizeSharpen</td>
                    <td>-</td>
                    <td>true</td>
                </tr>
                <tr>
                    <td>TempFolders</td>
                    <td>-</td>
                    <td>['thumbnail', 'medium', 'large', 'temp', 'zip']</td>
                </tr>
                <tr>
                    <td>ResizeDimensions</td>
                    <td>-</td>
                    <td>[320, 480, 1500]</td>
                </tr>
                <tr>
                    <td>DestinationPath</td>
                    <td>-</td>
                    <td>null</td>
                </tr>
                <tr>
                    <td>UploadExists (increment or overwrite)</td>
                    <td>-</td>
                    <td>increment</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->RenderEnd(); ?>

<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>


