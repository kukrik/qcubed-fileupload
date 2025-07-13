<?php
require_once('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Exception\Caller;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\Ajax;
use QCubed\Action\Server;
use QCubed\Event\Click;
use QCubed\Action\ActionParams;

    /**
     * Class ExamplesForm
     *
     * Represents a form with functionality for file uploads, language selection, and folder path input.
     * It configures various UI components such as buttons and text boxes to provide an interactive
     * user interface for uploading and managing files.
     */
    class ExamplesForm extends Form
{
    protected Q\Plugin\FileUpload $objUpload;
    protected Q\Plugin\BsFileControl $btnAddFiles;
    protected Bs\Button $btnAllStart;
    protected Bs\Button $btnAllCancel;

    protected Bs\Button $btnEn;
    protected Bs\Button $btnEt;
    protected Bs\Button $btnRu;
    protected Bs\TextBox $txtPathText;

    /**
     * Initializes buttons, text boxes, and file handling components for the form.
     *
     * This method configures language selection buttons, a text box for the folder path,
     * file upload controls, and other related UI components, including client-side actions
     * associated with these elements.
     *
     * @return void
     * @throws Caller
     */
    protected function formCreate(): void
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
        $this->btnAddFiles->addAction(new Click(), new Ajax('fileUpload_Click'));

        $this->btnAllStart = new Bs\Button($this);
        $this->btnAllStart->Text = t('Start upload');
        $this->btnAllStart->CssClass = 'btn btn-primary all-start disabled';
        $this->btnAllStart->PrimaryButton = true;
        $this->btnAllStart->UseWrapper = false;
        $this->btnAllStart->addAction(new Click(), new Ajax('fileSave_Click'));

        $this->btnAllCancel = new Bs\Button($this);
        $this->btnAllCancel->Text = t('Cancel all uploads');
        $this->btnAllCancel->CssClass = 'btn btn-warning all-cancel disabled';
        $this->btnAllCancel->UseWrapper = false;

        $this->objUpload = new Q\Plugin\FileUpload($this);
        $this->objUpload->Language = 'et'; // Default en
        //$this->objUpload->ShowIcons = true; // Default false
        //$this->objUpload->AcceptFileTypes = ['gif', 'jpg', 'jpeg', 'png', 'pdf', 'docx', 'mp4']; // Default null
        //$this->objUpload->MaxNumberOfFiles = 2; // Default null
        //$this->objUpload->MaxFileSize = 1024 * 1024 * 2; // 2 MB // Default null
        //$this->objUpload->MinFileSize = 500000; // 500 kb // Default null
        //$this->objUpload->ChunkUpload = false; // Default true
        $this->objUpload->MaxChunkSize = 1024 * 1024; // * 5; // * 10; // 10 MB // Default 5 MB
        //$this->objUpload->LimitConcurrentUploads = 5; // Default 2
        $this->objUpload->Url = 'php/'; // Default null
        //$this->objUpload->PreviewMaxWidth = 120; // Default 80
        //$this->objUpload->PreviewMaxHeight = 120; // Default 80
        //$this->objUpload->WithCredentials = true; // Default false
    }

    /**
     * Handles the click event for the file upload action.
     *
     * This method clears the 'name' session variable to reset any session-based data
     * related to the file upload process.
     *
     * @param ActionParams $params Parameters associated with the click event.
     *
     * @return void
     */
    protected function fileUpload_Click(ActionParams $params): void
    {
        unset( $_SESSION['name']);
    }

    /**
     * Handles the event triggered when save a button is clicked.
     *
     * @param ActionParams $params The parameters associated with the action/event.
     *
     * @return void
     */
    protected function fileSave_Click(ActionParams $params): void
    {
        $_SESSION['name'] = $this->txtPathText->Text;
        $this->txtPathText->Text = null;
    }

    /**
     * Handles the event triggered when the button is clicked.
     *
     * @param ActionParams $params The parameters associated with the action/event.
     *
     * @return void
     */
    protected function button_Click(ActionParams $params): void
    {
        $language = $params->ActionParameter;
        $this->objUpload->Language = $language;
    }

}
ExamplesForm::run('ExamplesForm');