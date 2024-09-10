<?php
require_once('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Plugin\UploadHandler;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\Ajax;
use QCubed\Action\Server;
use QCubed\Event\Click;
use QCubed\Action\ActionParams;

class ExamplesForm extends Form
{
    protected $objUpload;
    protected $btnAddFiles;
    protected $btnAllStart;
    protected $btnAllCancel;

    protected $btnEn;
    protected $btnEt;
    protected $btnRu;
    protected $txtPathText;

    /**
     * @return void
     * @throws Q\Exception\Caller
     */
    protected function formCreate()
    {
        parent::formCreate();

        ////////////////////////////

        $this->btnEn = new Bs\Button($this);
        $this->btnEn->Text = 'EN';
        $this->btnEn->ActionParameter = 'en';
        $this->btnEn->addAction(new Click(), new Server('button_Click'));

        $this->btnEt = new Bs\Button($this);
        $this->btnEt->Text = 'ET';
        $this->btnEt->ActionParameter = 'et';
        $this->btnEt->addAction(new Click(), new Server('button_Click'));

        $this->btnRu = new Bs\Button($this);
        $this->btnRu->Text = 'RU';
        $this->btnRu->ActionParameter = 'ru';
        $this->btnRu->addAction(new Click(), new Server('button_Click'));

        $this->txtPathText = new Bs\TextBox($this);
        $this->txtPathText->Placeholder = t('Create the destination path of the folder name');
        $this->txtPathText->setHtmlAttribute('autocomplete', 'off');
        $this->txtPathText->addWrapperCssClass('center-button');

        ////////////////////////////

        $this->btnAddFiles = new Q\Plugin\BsFileControl($this, 'files');
        $this->btnAddFiles->Text = t('Add files');
        $this->btnAddFiles->Multiple = true;
        $this->btnAddFiles->CssClass = 'btn btn-success fileinput-button';
        $this->btnAddFiles->UseWrapper = false;
        $this->btnAddFiles->addAction(new Q\Event\Click(), new Q\Action\Ajax('fileUpload_Click'));

        $this->btnAllStart = new Bs\Button($this);
        $this->btnAllStart->Text = t('Start upload');
        $this->btnAllStart->CssClass = 'btn btn-primary all-start disabled';
        $this->btnAllStart->PrimaryButton = true;
        $this->btnAllStart->UseWrapper = false;
        $this->btnAllStart->addAction(new Q\Event\Click(), new Q\Action\Ajax('fileSave_Click'));

        $this->btnAllCancel = new Bs\Button($this);
        $this->btnAllCancel->Text = t('Cancel all uploads');
        $this->btnAllCancel->CssClass = 'btn btn-warning all-cancel disabled';
        $this->btnAllCancel->UseWrapper = false;

        $this->objUpload = new Q\Plugin\FileUpload($this);
        //$this->objUpload->Language = 'et'; // Default en
        //$this->objUpload->ShowIcons = true; // Default false
        $this->objUpload->AcceptFileTypes = ['gif', 'jpg', 'jpeg', 'png', 'pdf', 'docx', 'mp4']; // Default null
        //$this->objUpload->MaxNumberOfFiles = 5; // Default null
        //$this->objUpload->MaxFileSize = 1024 * 1024 * 2; // 2 MB // Default null
        //$this->objUpload->MinFileSize = 500000; // 500 kb // Default null
        //$this->objUpload->ChunkUpload = false; // Default true
        $this->objUpload->MaxChunkSize = 1024 * 1024; // * 10; // 10 MB // Default 5 MB
        //$this->objUpload->LimitConcurrentUploads = 5; // Default 2
        $this->objUpload->Url = 'php/'; // Default null
        //$this->objUpload->PreviewMaxWidth = 120; // Default 80
        //$this->objUpload->PreviewMaxHeight = 120; // Default 80
        //$this->objUpload->WithCredentials = true; // Default false
    }

    protected function fileUpload_Click(ActionParams $params)
    {
        unset( $_SESSION['name']);
    }

    protected function fileSave_Click(ActionParams $params)
    {
        $_SESSION['name'] = $this->txtPathText->Text;
        $this->txtPathText->Text = null;
    }

    protected function button_Click(ActionParams $params)
    {
        $language = $params->ActionParameter;
        $this->objUpload->Language = $language;
    }

}
ExamplesForm::Run('ExamplesForm');