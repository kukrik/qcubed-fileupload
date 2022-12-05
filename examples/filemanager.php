<?php
require_once('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Bootstrap\Bootstrap;
use QCubed\Plugin\VauuTable;
use QCubed\Project\Control\Paginator;

use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Event\Change;
use QCubed\Event\Input;
use QCubed\Event\EnterKey;
use QCubed\Event\Click;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\QString;
use QCubed\Query\QQ;

class ExamplesForm extends Form
{
    protected $dlgModal1;
    protected $dlgModal2;

    protected $dtgFiles;
    protected $lstItemsPerPage;
    protected $txtFilter;

    protected $txtFileName;
    protected $btnSave;
    protected $btnCancel;

    protected $strDateTimeFormat = 'd.m.Y H:i';
    protected $intEditFilesId = null;
    protected $intDeleteId = null;

    protected $tempFolders = ['thumbnail', 'medium', 'large'];

    ////////////////////////////

    /**
     * @return void
     * @throws Q\Exception\Caller
     */
    protected function formCreate()
    {
        parent::formCreate();

        ////////////////////////////

        $this->dtgFiles = new VauuTable($this);
        $this->dtgFiles->addCssFile(QCUBED_BOOTSTRAP_CSS);
        $this->dtgFiles->addCssFile(QCUBED_FILEUPLOAD_ASSETS_URL . '/css/custom.css');
        $this->dtgFiles->CssClass = "table vauu-table table-hover table-responsive";

        $col = $this->dtgFiles->createCallableColumn('View', [$this, 'View_render']);
        $col->HtmlEntities = false;
        $col->CellStyler->Width = '7%';

        $col = $this->dtgFiles->createCallableColumn('Name', [$this, 'Name_render']);
        $col->HtmlEntities = false;
        $col->CellStyler->Width = '28%' ;

        $col = $this->dtgFiles->createCallableColumn('Extension', [$this, 'Extension_render']);
        $col->CellStyler->Width = '10%';

        $col = $this->dtgFiles->createCallableColumn('Size', [$this, 'Size_render']);
        $col->CellStyler->Width = '10%';

        $col = $this->dtgFiles->createCallableColumn('Modified', [$this, 'Date_render']);
        $col->CellStyler->Width = '15%';

        $col = $this->dtgFiles->createNodeColumn('Dimensions', QQN::Files()->Dimensions);
        $col->CellStyler->Width = '15%';

        $col = $this->dtgFiles->createCallableColumn('Edit', [$this, 'Edit_render']);
        $col->HtmlEntities = false;
        //$col->CellStyler->Width = 200;

        $this->dtgFiles->Paginator = new Bs\Paginator($this);
        $this->dtgFiles->Paginator->LabelForPrevious = 'Previous';
        $this->dtgFiles->Paginator->LabelForNext = 'Next';
        $this->dtgFiles->ItemsPerPage = 10;

        $this->dtgFiles->UseAjax = true;

        $this->dtgFiles->setDataBinder('dtgFiles_Bind', $this);

        ////////////////////////////

        $this->lstItemsPerPage = new Q\Plugin\Select2($this);
        $this->lstItemsPerPage->addCssFile(QCUBED_FILEUPLOAD_ASSETS_URL . '/css/select2-web-vauu.css');
        $this->lstItemsPerPage->MinimumResultsForSearch = -1;
        $this->lstItemsPerPage->Theme = 'web-vauu';
        $this->lstItemsPerPage->Width = '100%';
        $this->lstItemsPerPage->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstItemsPerPage->SelectedValue = $this->dtgFiles->ItemsPerPage;
        $this->lstItemsPerPage->addItems(array(10, 25, 50, 100));
        $this->lstItemsPerPage->AddAction(new Change(), new Ajax('lstItemsPerPage_Change'));

        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();

        ////////////////////////////

        $this->txtFileName = new Bs\TextBox($this->dtgFiles);
        $this->txtFileName->Required = true;

        $this->btnSave = new Bs\Button($this->dtgFiles);
        $this->btnSave->Text = 'Save';
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addAction(new Click(), new Ajax('btnSave_Click'));
        $this->btnSave->PrimaryButton = true;

        $this->btnCancel = new Bs\Button($this->dtgFiles);
        $this->btnCancel->Text = 'Cancel';
        $this->btnCancel->addAction(new Click(), new Ajax('btnCancel_Click'));
        $this->btnCancel->CausesValidation = false;

        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = '<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this file?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>';
        $this->dlgModal1->Title = 'Warning';
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton("I accept", 'This file has been permanently deleted', false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('deletedItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = '<p style="line-height: 25px; margin-bottom: 2px;">File name changed successfully!</p>';
        $this->dlgModal2->Title = t("Success");
        $this->dlgModal2->HeaderClasses = 'btn-success';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

    }

    protected function lstItemsPerPage_Change(ActionParams $params)
    {
        $this->dtgFiles->ItemsPerPage = $this->lstItemsPerPage->SelectedName;
        $this->dtgFiles->refresh();
    }

    protected function addFilterActions()
    {
        $this->txtFilter->addAction(new Input(300), new Ajax('filterChanged'));
        $this->txtFilter->addActionArray(new EnterKey(),
            [
                new Ajax('FilterChanged'),
                new Terminate()
            ]
        );
    }

    protected function filterChanged()
    {
        $this->dtgFiles->refresh();
    }

    protected function dtgFiles_Bind()
    {
        $strSearchValue = $this->txtFilter->Text;
        $strSearchValue = trim($strSearchValue);

        if (is_null($strSearchValue) || $strSearchValue === '') {
            $objCondition = QQ::all();
        } else {
            $objCondition = QQ::orCondition(
                QQ::like(QQN::Files()->Name, "%" . $strSearchValue . "%"),
                QQ::like(QQN::Files()->Extension, "%" . $strSearchValue . "%"),
                QQ::like(QQN::Files()->Mtime, $strSearchValue),
                QQ::like(QQN::Files()->Dimensions, "%" . $strSearchValue . "%")
            );
        }

        $this->dtgFiles->TotalItemCount = Files::countAll();

        $objClauses = array();
        if ($objClause = $this->dtgFiles->OrderByClause)
            $objClauses[] = $objClause;
        if ($objClause = $this->dtgFiles->LimitClause)
            $objClauses[] = $objClause;

        $this->dtgFiles->DataSource = Files::QueryArray($objCondition, $objClauses);
    }

    protected function formPreRender()
    {
        $this->dtgFiles->refresh();
    }

    public function View_render(Files $objFiles)
    {
        $allowed = array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
        $ext = strtolower(pathinfo(APP_UPLOADS_DIR . $objFiles->Path, PATHINFO_EXTENSION));

        $strHtm = '<span class="preview">';
        if (in_array($ext, $allowed)) {
            $strHtm .= '<img src="' . APP_UPLOADS_TEMP_URL . '/_files/thumbnail' . $objFiles->Path . '">';
        } else {
            $strHtm .= $this->getFileIconExtension($ext);
        }
        $strHtm .= '</span>';
        return $strHtm;
    }

    public function Name_render(Files $objFiles)
    {
        if ($objFiles->Id == $this->intEditFilesId) {
            return $this->txtFileName->render(false);
        } else {
            return QString::htmlEntities($objFiles->Name);
        }
    }

    public function Extension_render(Files $objFiles)
    {
        return strtoupper($objFiles->Extension);
    }

    public function Size_render(Files $objFiles)
    {
        return $this->readableBytes($objFiles->Size);
    }

    public function Date_render(Files $objFiles)
    {
        return date($this->strDateTimeFormat, $objFiles->Mtime);
    }

    public function Edit_render(Files $objFiles)
    {
        if ($objFiles->Id == $this->intEditFilesId) {
            return $this->btnSave->render(false) . ' ' . $this->btnCancel->render(false);
        } else {
            $btnEditId = 'btnEdit' . $objFiles->Id;

            if (!$btnEdit = $this->getControl($btnEditId)) {
            $btnEdit = new Bs\Button($this->dtgFiles, $btnEditId);
            $btnEdit->Text = 'Edit';
            $btnEdit->ActionParameter = $objFiles->Id;
            $btnEdit->CssClass = 'btn btn-orange';
            $btnEdit->UseWrapper = false;
            $btnEdit->addAction(new Click(), new Ajax('btnEdit_Click'));
            $btnEdit->CausesValidation = false;
        }
            $btnDeleteId = 'btnDelete' . $objFiles->Id;

            if (!$btnDelete = $this->getControl($btnDeleteId)) {
            $btnDelete = new Bs\Button($this->dtgFiles, $btnDeleteId);
            $btnDelete->Text = 'Delete';
            $btnDelete->ActionParameter = $objFiles->Id;
            $btnDelete->UseWrapper = false;
            $btnDelete->addAction(new Click(), new Ajax('btnDelete_Click'));
            $btnDelete->CausesValidation = false;
            }

            return $btnEdit->render(false) . ' ' . $btnDelete->render(false);
        }
    }

    public function btnEdit_Click(ActionParams $params) // ActionParams $params
    {
        $this->intEditFilesId = intval($params->ActionParameter);
        $obj = Files::load($this->intEditFilesId);
        $this->txtFileName->Text = pathinfo(APP_UPLOADS_DIR . $obj->Path, PATHINFO_FILENAME);
        Application::executeControlCommand($this->txtFileName->ControlId, 'focus');
    }

    protected function btnDelete_Click(ActionParams $params)
    {
        $this->intDeleteId = intval($params->ActionParameter);
        $obj = Files::load($this->intDeleteId);
        $this->dlgModal1->showDialogBox();
    }

    protected function deletedItem_Click(ActionParams $params)
    {
        $obj = Files::load($this->intDeleteId);

        if (is_file(APP_UPLOADS_DIR . $obj->Path)) {
            unlink(APP_UPLOADS_DIR . $obj->Path);

            foreach ($this->tempFolders as $tempFolder) {
                if (is_file(APP_UPLOADS_TEMP_DIR . '/_files/' . $tempFolder . $obj->Path)) {
                    unlink(APP_UPLOADS_TEMP_DIR . '/_files/' . $tempFolder . $obj->Path);
                }
            }
        }
        $obj->delete();
        $this->dlgModal1->hideDialogBox();
    }

    protected function btnSave_Click(ActionParams $params)
    {
        $obj = Files::load($this->intEditFilesId);

        if ($this->txtFileName->Text) {
            if (is_file(APP_UPLOADS_DIR . $obj->Path)) {

                $parts = pathinfo(APP_UPLOADS_DIR . $obj->Path);
                $newPath = $parts['dirname'] . '/' . trim($this->txtFileName->Text) . '.' . strtolower($parts['extension']);
                $this->rename(APP_UPLOADS_DIR . $obj->Path, $newPath);

                foreach ($this->tempFolders as $tempFolder) {
                    if (is_file(APP_UPLOADS_TEMP_DIR . '/_files/' . $tempFolder . $obj->Path)) {
                        $this->rename(APP_UPLOADS_TEMP_DIR . '/_files/' . $tempFolder . $obj->Path, APP_UPLOADS_TEMP_DIR . '/_files/' . $tempFolder . $this->getRelativePath($newPath));
                    }
                }
            }
        }

        $obj->Name = basename($newPath);
        $obj->Path = $this->getRelativePath($newPath);
        $obj->Mtime = time();
        $obj->save();

        $this->intEditFilesId = null;
        $this->dlgModal2->showDialogBox();
    }

    protected function btnCancel_Click(ActionParams $params)
    {
        $this->intEditFilesId = null;
    }

    protected function delete($path)
    {
        

        if (is_file($path)) {


        }
    }

    protected function rename($old, $new)
    {
        return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
    }

    protected function getRelativePath($path)
    {
        return substr($path, strlen(APP_UPLOADS_DIR));
    }

    protected static function getFileIconExtension($ext)
    {
        switch ($ext) {
            case 'gif':
            case 'jpg':
            case 'jpeg':
            case 'jpc':
            case 'png':
            case 'bmp':
                $icon = '<svg class="svg-file svg-image files-svg" viewBox="0 0 56 56"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"> <circle cx="18.931" cy="14.431" r="4.569" style="fill:#f3d55b"></circle> <polygon points="6.5,39 17.5,39 49.5,39 49.5,28 39.5,18.5 29,30 23.517,24.517" style="fill:#88c057"></polygon> </g> <path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path> <text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text> </svg>';
                break;
            case 'pdf':
                $icon = '<svg class="svg-file svg-pdf files-svg" viewBox="0 0 56 56"> <path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M19.514,33.324L19.514,33.324c-0.348,0-0.682-0.113-0.967-0.326 c-1.041-0.781-1.181-1.65-1.115-2.242c0.182-1.628,2.195-3.332,5.985-5.068c1.504-3.296,2.935-7.357,3.788-10.75 c-0.998-2.172-1.968-4.99-1.261-6.643c0.248-0.579,0.557-1.023,1.134-1.215c0.228-0.076,0.804-0.172,1.016-0.172 c0.504,0,0.947,0.649,1.261,1.049c0.295,0.376,0.964,1.173-0.373,6.802c1.348,2.784,3.258,5.62,5.088,7.562 c1.311-0.237,2.439-0.358,3.358-0.358c1.566,0,2.515,0.365,2.902,1.117c0.32,0.622,0.189,1.349-0.39,2.16 c-0.557,0.779-1.325,1.191-2.22,1.191c-1.216,0-2.632-0.768-4.211-2.285c-2.837,0.593-6.15,1.651-8.828,2.822 c-0.836,1.774-1.637,3.203-2.383,4.251C21.273,32.654,20.389,33.324,19.514,33.324z M22.176,28.198 c-2.137,1.201-3.008,2.188-3.071,2.744c-0.01,0.092-0.037,0.334,0.431,0.692C19.685,31.587,20.555,31.19,22.176,28.198z M35.813,23.756c0.815,0.627,1.014,0.944,1.547,0.944c0.234,0,0.901-0.01,1.21-0.441c0.149-0.209,0.207-0.343,0.23-0.415 c-0.123-0.065-0.286-0.197-1.175-0.197C37.12,23.648,36.485,23.67,35.813,23.756z M28.343,17.174 c-0.715,2.474-1.659,5.145-2.674,7.564c2.09-0.811,4.362-1.519,6.496-2.02C30.815,21.15,29.466,19.192,28.343,17.174z M27.736,8.712c-0.098,0.033-1.33,1.757,0.096,3.216C28.781,9.813,27.779,8.698,27.736,8.712z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>';
                break;
            case 'docx':
            case 'doc':
                $icon = '<svg class="svg-file svg-word files-svg" viewBox="0 0 56 56"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M12.5,13h6c0.553,0,1-0.448,1-1s-0.447-1-1-1h-6c-0.553,0-1,0.448-1,1S11.947,13,12.5,13z"></path><path d="M12.5,18h9c0.553,0,1-0.448,1-1s-0.447-1-1-1h-9c-0.553,0-1,0.448-1,1S11.947,18,12.5,18z"></path><path d="M25.5,18c0.26,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 c-0.38-0.37-1.04-0.37-1.42,0c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71C24.979,17.89,25.24,18,25.5,18z"></path><path d="M29.5,18h8c0.553,0,1-0.448,1-1s-0.447-1-1-1h-8c-0.553,0-1,0.448-1,1S28.947,18,29.5,18z"></path><path d="M11.79,31.29c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71 C11.979,32.89,12.229,33,12.5,33c0.27,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 C12.84,30.92,12.16,30.92,11.79,31.29z"></path><path d="M24.5,31h-8c-0.553,0-1,0.448-1,1s0.447,1,1,1h8c0.553,0,1-0.448,1-1S25.053,31,24.5,31z"></path><path d="M41.5,18h2c0.553,0,1-0.448,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.448-1,1S40.947,18,41.5,18z"></path><path d="M12.5,23h22c0.553,0,1-0.448,1-1s-0.447-1-1-1h-22c-0.553,0-1,0.448-1,1S11.947,23,12.5,23z"></path><path d="M43.5,21h-6c-0.553,0-1,0.448-1,1s0.447,1,1,1h6c0.553,0,1-0.448,1-1S44.053,21,43.5,21z"></path><path d="M12.5,28h4c0.553,0,1-0.448,1-1s-0.447-1-1-1h-4c-0.553,0-1,0.448-1,1S11.947,28,12.5,28z"></path><path d="M30.5,26h-10c-0.553,0-1,0.448-1,1s0.447,1,1,1h10c0.553,0,1-0.448,1-1S31.053,26,30.5,26z"></path><path d="M43.5,26h-9c-0.553,0-1,0.448-1,1s0.447,1,1,1h9c0.553,0,1-0.448,1-1S44.053,26,43.5,26z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>';
                break;
            case 'xlsx':
            case 'xls':
                $icon = '<svg viewBox="0 0 56 56" class="svg-file svg-excel files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path style="fill:#c8bdb8" d="M23.5,16v-4h-12v4v2v2v2v2v2v2v2v4h10h2h21v-4v-2v-2v-2v-2v-2v-4H23.5z M13.5,14h8v2h-8V14z M13.5,18h8v2h-8V18z M13.5,22h8v2h-8V22z M13.5,26h8v2h-8V26z M21.5,32h-8v-2h8V32z M42.5,32h-19v-2h19V32z M42.5,28h-19v-2h19V28 z M42.5,24h-19v-2h19V24z M23.5,20v-2h19v2H23.5z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>';
                break;
            case 'pptx':
            case 'ppt':
                $icon = '<svg viewBox="0 0 56 56" class="svg-file svg-powerpoint files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path style="fill:#c8bdb8" d="M39.5,30h-24V14h24V30z M17.5,28h20V16h-20V28z"></path><path style="fill:#c8bdb8" d="M20.499,35c-0.175,0-0.353-0.046-0.514-0.143c-0.474-0.284-0.627-0.898-0.343-1.372l3-5 c0.284-0.474,0.898-0.627,1.372-0.343c0.474,0.284,0.627,0.898,0.343,1.372l-3,5C21.17,34.827,20.839,35,20.499,35z"></path><path style="fill:#c8bdb8" d="M34.501,35c-0.34,0-0.671-0.173-0.858-0.485l-3-5c-0.284-0.474-0.131-1.088,0.343-1.372 c0.474-0.283,1.088-0.131,1.372,0.343l3,5c0.284,0.474,0.131,1.088-0.343,1.372C34.854,34.954,34.676,35,34.501,35z"></path><path style="fill:#c8bdb8" d="M27.5,16c-0.552,0-1-0.447-1-1v-3c0-0.553,0.448-1,1-1s1,0.447,1,1v3C28.5,15.553,28.052,16,27.5,16 z"></path><rect x="17.5" y="16" style="fill:#d3ccc9" width="20" height="12"></rect></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>';
                break;
            case 'mov':
            case 'mpeg':
            case 'mpg':
            case 'mp4':
            case 'm4v':
                $icon = '<svg viewBox="0 0 56 56" class="svg-file svg-video files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path>\<polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M24.5,28c-0.166,0-0.331-0.041-0.481-0.123C23.699,27.701,23.5,27.365,23.5,27V13 c0-0.365,0.199-0.701,0.519-0.877c0.321-0.175,0.71-0.162,1.019,0.033l11,7C36.325,19.34,36.5,19.658,36.5,20 s-0.175,0.66-0.463,0.844l-11,7C24.874,27.947,24.687,28,24.5,28z M25.5,14.821v10.357L33.637,20L25.5,14.821z"></path><path d="M28.5,35c-8.271,0-15-6.729-15-15s6.729-15,15-15s15,6.729,15,15S36.771,35,28.5,35z M28.5,7 c-7.168,0-13,5.832-13,13s5.832,13,13,13s13-5.832,13-13S35.668,7,28.5,7z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>';
                break;
            case 'wav':
            case 'mp3':
            case 'mp2':
            case 'm4a':
            case 'aac':
                $icon = '<svg viewBox="0 0 56 56" class="svg-file svg-audio files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M35.67,14.986c-0.567-0.796-1.3-1.543-2.308-2.351c-3.914-3.131-4.757-6.277-4.862-6.738V5 c0-0.553-0.447-1-1-1s-1,0.447-1,1v1v8.359v9.053h-3.706c-3.882,0-6.294,1.961-6.294,5.117c0,3.466,2.24,5.706,5.706,5.706 c3.471,0,6.294-2.823,6.294-6.294V16.468l0.298,0.243c0.34,0.336,0.861,0.72,1.521,1.205c2.318,1.709,6.2,4.567,5.224,7.793 C35.514,25.807,35.5,25.904,35.5,26c0,0.43,0.278,0.826,0.71,0.957C36.307,26.986,36.404,27,36.5,27c0.43,0,0.826-0.278,0.957-0.71 C39.084,20.915,37.035,16.9,35.67,14.986z M26.5,27.941c0,2.368-1.926,4.294-4.294,4.294c-2.355,0-3.706-1.351-3.706-3.706 c0-2.576,2.335-3.117,4.294-3.117H26.5V27.941z M31.505,16.308c-0.571-0.422-1.065-0.785-1.371-1.081l-1.634-1.34v-3.473 c0.827,1.174,1.987,2.483,3.612,3.783c0.858,0.688,1.472,1.308,1.929,1.95c0.716,1.003,1.431,2.339,1.788,3.978 C34.502,18.515,32.745,17.221,31.505,16.308z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>';
                break;
            case 'rtf':
            case 'txt':
                $icon = '<svg viewBox="0 0 56 56" class="svg-file svg-text files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M12.5,13h6c0.553,0,1-0.448,1-1s-0.447-1-1-1h-6c-0.553,0-1,0.448-1,1S11.947,13,12.5,13z"></path><path d="M12.5,18h9c0.553,0,1-0.448,1-1s-0.447-1-1-1h-9c-0.553,0-1,0.448-1,1S11.947,18,12.5,18z"></path><path d="M25.5,18c0.26,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 c-0.38-0.37-1.04-0.37-1.42,0c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71C24.979,17.89,25.24,18,25.5,18z"></path><path d="M29.5,18h8c0.553,0,1-0.448,1-1s-0.447-1-1-1h-8c-0.553,0-1,0.448-1,1S28.947,18,29.5,18z"></path><path d="M11.79,31.29c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71 C11.979,32.89,12.229,33,12.5,33c0.27,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 C12.84,30.92,12.16,30.92,11.79,31.29z"></path><path d="M24.5,31h-8c-0.553,0-1,0.448-1,1s0.447,1,1,1h8c0.553,0,1-0.448,1-1S25.053,31,24.5,31z"></path><path d="M41.5,18h2c0.553,0,1-0.448,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.448-1,1S40.947,18,41.5,18z"></path><path d="M12.5,23h22c0.553,0,1-0.448,1-1s-0.447-1-1-1h-22c-0.553,0-1,0.448-1,1S11.947,23,12.5,23z"></path><path d="M43.5,21h-6c-0.553,0-1,0.448-1,1s0.447,1,1,1h6c0.553,0,1-0.448,1-1S44.053,21,43.5,21z"></path><path d="M12.5,28h4c0.553,0,1-0.448,1-1s-0.447-1-1-1h-4c-0.553,0-1,0.448-1,1S11.947,28,12.5,28z"></path><path d="M30.5,26h-10c-0.553,0-1,0.448-1,1s0.447,1,1,1h10c0.553,0,1-0.448,1-1S31.053,26,30.5,26z"></path><path d="M43.5,26h-9c-0.553,0-1,0.448-1,1s0.447,1,1,1h9c0.553,0,1-0.448,1-1S44.053,26,43.5,26z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>';
                break;
            case 'zip':
            case 'rar':
            case 'asice':
            case 'cdoc':
                $icon = '<svg viewBox="0 0 56 56" class="svg-file svg-archive files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M28.5,24v-2h2v-2h-2v-2h2v-2h-2v-2h2v-2h-2v-2h2V8h-2V6h-2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2 h-4v5c0,2.757,2.243,5,5,5s5-2.243,5-5v-5H28.5z M30.5,29c0,1.654-1.346,3-3,3s-3-1.346-3-3v-3h6V29z"></path><path d="M26.5,30h2c0.552,0,1-0.447,1-1s-0.448-1-1-1h-2c-0.552,0-1,0.447-1,1S25.948,30,26.5,30z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>';
                break;
                default:
                    $icon = '<svg viewBox="0 0 56 56" class="svg-file svg-none files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext f_10" x="28" y="51.5">' . $ext . '</text></svg>';
        }
        return $icon;
    }

    protected function readableBytes($bytes)
    {
        $i = floor(log($bytes) / log(1024));
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
    }
}
ExamplesForm::Run('ExamplesForm');