<?php

set_time_limit( 180 );

class xliffToTargetController extends downloadController {

    protected $error;
    protected $errorMessage;

    public function doAction() {
        // Just add the XLIFF extension, the DetectProprietaryXliff class needs it
        $file_path       = $_FILES['xliff']['tmp_name'] . '.xlf';
        move_uploaded_file($_FILES['xliff']['tmp_name'], $file_path);

        // Detect XLIFF type
        $fileInfo = DetectProprietaryXliff::getInfo($file_path);
        $converterVersion = $fileInfo['converter_version'];

        $converter = new FileFormatConverter($converterVersion);

        $conversion = $converter->multiConvertToOriginal(array(
          1 => array(
            'document_content' => file_get_contents($file_path))
        ));

        if ($conversion[1]['isSuccess'] !== true) {
            $this->error = true;
            $this->errorMessage =  $conversion[1]['errorMessage'];
        } else {
            $this->content = json_encode(array(
              "fileName" => $conversion[1]['filename'],
              "fileContent" => base64_encode($conversion[1]['document_content']),
              "size" => filesize($file_path),
              "type" => mime_content_type($file_path),
              "message" => "File downloaded! Check your download folder"
            ));
        }
    }

    public function finalize() {
        if ($this->error === true) {
            http_response_code(500);
            if (empty($this->errorMessage)) {
                echo "(No error message provided)";
            } else {
                echo $this->errorMessage;
            }
            exit;
        } else {
            try {
                $buffer = ob_get_clean();
                ob_start("ob_gzhandler");  // compress page before sending
                $this->nocache();
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                header("Content-Disposition: attachment; filename=\"$this->_filename\""); // enclose file name in double quotes in order to avoid duplicate header error. Reference https://github.com/prior/prawnto/pull/16
                header("Expires: 0");
                header("Connection: close");
                echo $this->content;
                exit;
            } catch (Exception $e) {
                echo "<pre>";
                print_r($e);
                echo "\n\n\n</pre>";
                exit;
            }
        }
    }

}
