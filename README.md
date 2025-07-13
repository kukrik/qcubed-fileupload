# QCubed-4 FileUpload


## QCubed-4 plugin created for FileUpload

Many third-party plugins are difficult or inconvenient to integrate with QCubed-4. In this case, we decided to build 
the Fileupload plugin to fit the QCubed-4 framework as much as possible. Here we use JavaScript and a bit of jQuery. 
This plugin works starting from PHP version 8.3+.

See FileUploadBase and UploadHandler class for usage and configuration descriptions and use cases.

![Image of kukrik](screenshot/examples_screenshot.png?raw=true)

### Requirements
First, you must check whether the conditions are met:

- Does the "upload" directory exist in /project/assets.
- Does the "tmp" directory exist in /project.
- Please check if the constants 'APP_UPLOADS_URL', 'APP_UPLOADS_DIR', 'APP_UPLOADS_TEMP_URL', 'APP_UPLOADS_TEMP_DIR' exist in
https://github.com/qcubed-4/application/blob/master/install/project/includes/configuration/active/2directories.cfg.php#L29

### Options
Note: If you want to override some properties, the same properties must be overridden to the same value
in FileUpload and Uploadhandler.

The rest of the properties can be set as needed.

| Property | FileUpload | UploadHandler |
|------| --- |---|
| RootPath | APP_UPLOADS_DIR | APP_UPLOADS_DIR |
| RootUrl | APP_UPLOADS_URL | - |
| TempPath | APP_UPLOADS_TEMP_DIR | APP_UPLOADS_TEMP_DIR |
| TempUrl | APP_UPLOADS_TEMP_URL | - |
| StoragePath | _files | _files |
| FullStoragePath | null | null |
| Language (en, et, ru) | en | - |
| ShowIcons | false | - |
| AcceptFileTypes | null | null |
| MaxNumberOfFiles | null | - |
| MaxFileSize | null | null |
| MinFileSize | null | - |
| ChunkUpload | true | - |
| MaxChunkSize | 5 MB | - |
| LimitConcurrentUploads | 2 | - |
| Url | null | - |
| PreviewMaxWidth | 80 | - |
| PreviewMaxHeight | 80 | - |
| WithCredentials | false | - |
| ImageResizeQuality | - | 85 |
| PngLevel | - | 6 |
| ImageResizeFunction ( imagecopyresampled or imagecopyresized) | - | imagecopyresampled |
| ImageResizeSharpen | - | true |
| TempFolders | - | ['thumbnail', 'medium', 'large'] |
| ResizeDimensions | - | [320, 480, 1500] |
| DestinationPath | - | null |
| UploadExists (increment or overwrite) | - | increment |


If you have not previously installed QCubed-4 Bootstrap and twitter bootstrap, run the following actions on the command line of your main installation directory by Composer:

```
    composer require twbs/bootstrap v3.3.7
```
and

```
    composer require kukrik/qcubed-fileupload
    composer require qcubed-4/plugin-bootstrap
```

