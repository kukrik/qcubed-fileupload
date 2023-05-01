<?php require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<style>
   body {font-size: 14px;}
    /*p, */footer {font-size: medium;}
    footer {margin-top: 35px;}
</style>

<?php $this->RenderBegin(); ?>

<div class="instructions">
    <h1 class="instruction_title" style="padding-bottom: 15px;">QCubed v4 plugin created for FileUpload</h1>
    <p>Many third-party plugins are difficult or inconvenient to fit for QCubed-4.
        Here it was decided to build the Fileupload plugin to fit the QCubed-4 framework as much as possible. Here we
        use javascript, a bit of jquery and PHP.</p>
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

    <h3>One example</h3>
    <p>This is just a quick example. Language translations are intended to activate javascript language translation.
        Currently, English, Estonian and Russian are available. The default language is English.</p>
    <p>First you need to know that this plugin has been created with a specific purpose in the direction that if you
        want to create a new folder in the "upload" directory. Then you must also create a folder with the same name
        in the existing folders "thumbnail", "medium", "large" with exactly the same depth.</p>
    <p>On the left is a simple and dirty example. This is to understand how to upload files to other folders in the upload
        directory. First, for example, create a new folder called "test" and put the same folder in
        the "upload", "thumbnail", "medium" and "large" directories. Write "test" in the text box and upload some files.
        Files are uploaded to the directories "upload/test", "thumbnail/test", "medium/test" and "large/test", etc...</p>
    <p>Note: This example uses $_SESSION. Please do not use it in session production. Development and production must
        use either $_GET or $_POST data passed to options[''DestinationPath''].</p>
    <h3>Two example</h3>
    <p>The FileUpload class only deals with uploading files and does not deal with direct file deletion after uploading.
        Before uploading, you can review the uploaded files again and discard some files.
        For example, a file manager is suitable for managing files and creating folders.</p>
    <hr>
</div>
<div class="container">
    <h3>Example One</h3>
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
                    <td>ImageResizeFunction ( imagecopyresampled or imagecopyresized)</td>
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
                    <td>['thumbnail', 'medium', 'large']</td>
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


