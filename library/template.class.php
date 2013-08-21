<?php
     /***************************************************/
    /*             PHP Class TemplateMaker             */
   /*     Creates HTML output from template files     */
  /*            ©2011 Geek Cave Creations            */
 /*              Coded by Dave Morton               */
/***************************************************/

  class Template {
    public $replacements;

    public function __construct($file) {
      if (!file_exists($file)) return $this->throwError("File $file does not exist!");
      $this->file = $file;
      $this->rawTemplate = file_get_contents($file, true);
      $this->sectionStart = '<!-- Section [section] Start -->';
      $this->sectionEnd = '<!-- Section [section] End -->';
    }

    public function buildFullTemplate($sections, $replacementTags, $useRegEx = false) {
      $content = '';
      foreach ($sections as $section => $notFoundReturn) {
        $content .= getSection($section, $notFoundReturn);
      }
      if ($useRegEx){
        $search = array_keys($replacementTags);
        $replace = array_values($replacementTags);
        $content = preg_replace($search, $replace, $content);
      }
      else {
        foreach ($replacementTags as $search => $replace) {
          $content = str_replace($search, $replace, $content);
        }
      }
      return $content;
    }

    public function getSection($sectionName, $notFoundReturn = false) {
      $sectionStart = $this->sectionStart;
      $sectionEnd = $this->sectionEnd;
      $rawTemplate = $this->rawTemplate;
      $start = str_replace('[section]', $sectionName, $sectionStart);
      $sectionStartLen = strlen($start);
      $end   = str_replace('[section]', $sectionName, $sectionEnd);
      $startPos = strpos($rawTemplate, $start, 0);
      if ($startPos === false) {
        if ($notFoundReturn) {
          return "\n";
        }
        else $startPos = 0;
      }
      else $startPos += $sectionStartLen;
      $endPos = strpos($rawTemplate, $end, $startPos) - 1;
      $sectionLen = $endPos - $startPos;
      $out = substr($rawTemplate, $startPos, $sectionLen);
      return trim($out);
    }

    protected function throwError($message) {
      die($message);
    }
  }
?>