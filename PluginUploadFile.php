<?php
/**
 * 
 * Improvements.
 * 180128: Add param success/before_save to run method before save image.
 * Windows: Has to add account IUSR to temp folder with proper privileges.
 */
class PluginUploadFile{
  /**
   * 
   */
  function __construct($buto){
    if($buto){
      wfPlugin::includeonce('wf/array');
      wfPlugin::includeonce('wf/yml');
    }
  }
  /**
   * 
   */
  public static function widget_include(){
    $element = array();
    $element[] = wfDocument::createHtmlElement('script', null, array('src' => '/plugin/upload/file/plugin_upload_file.js'));
    $element[] = wfDocument::createHtmlElement('script', "var plugin_upload_file = {};");
    wfDocument::renderElement($element);
  }
  /**
   * Use this widget to only view or access file outside the upload procedure.
   */
  public function widget_view($data){
    $data = new PluginWfArray($data);
    $data = new PluginWfArray($data->get('data'));
    $img_style = 'width:100%';
    if($data->get('img/style')){
      $img_style = $data->get('img/style');
    }
    /**
     * 
     */
    $fullname = wfSettings::replaceDir($data->get('dir').'/'.$data->get('name'));
    $element = array();
    if(wfFilesystem::fileExist($fullname)){
      /**
       * 
       */
      $fullname = wfSettings::replaceDir($data->get('web_dir')).'/'.$data->get('name').'?x='.wfCrypt::getUid();
      /**
       * Display element.
       */
      if($this->isExtension(array('png', 'jpg', 'gif'), $data->get('name'))){
        $element[] = wfDocument::createHtmlElement('img', null, array('src' => $fullname, 'class' => 'img-thumbnail', 'style' => $img_style));
      }else if($this->isExtension(array('pdf', 'yml', 'txt'), $data->get('name'))){
        $element[] = wfDocument::createHtmlElement('a', $data->get('name'), array('onclick' => "window.open('$fullname')"));
      }
    }elseif($data->get('image_not_exist')){
      $element = $data->get('image_not_exist');
    }
    /**
     * Render.
     */
    wfDocument::renderElement($element);
  }
  /**
   * 
   */
  public function widget_element($data){
    /**
     * 
     */
    $data = new PluginWfArray($data);
    $data = new PluginWfArray($data->get('data'));
    /**
     * Handle url.
     */
    if(!strstr($data->get('url'), '_time=')){
      if(strstr($data->get('url'), '?')){
        $data->set('url', $data->get('url').'&_time='.wfCrypt::getUid());
      }else{
        $data->set('url', $data->get('url').'?_time='.wfCrypt::getUid());
      }
    }
    /**
     * 
     */
    $fullname = wfSettings::replaceDir($data->get('dir').'/'.$data->get('name'));
    if(wfFilesystem::fileExist($fullname)){
      if($data->get('if_file_exist/element')){
        wfDocument::renderElement($data->get('if_file_exist/element'));
        return null;
      }
      /**
       * 
       */
      $fullname = wfSettings::replaceDir($data->get('web_dir')).'/'.$data->get('name').'?x='.wfCrypt::getUid();
      $element = array();
      /**
       * Buttons.
       */
      $element2 = array();
      $element2[] = wfDocument::createHtmlElement('button', 'Delete', array('onclick' => "PluginWfAjax.load('".$data->get('id')."_delete', '".$data->get('url')."');", 'id' => "".$data->get('id')."_delete", 'class' => 'btn btn-default'));
      $element2[] = wfDocument::createHtmlElement('button', 'View', array('onclick' => "window.open('$fullname')", 'class' => 'btn btn-default'));
      $element[] = wfDocument::createHtmlElement('p', $element2);
      /**
       * Display element.
       */
      $element2 = array();
      if($this->isExtension(array('png', 'jpg', 'gif'), $data->get('name'))){
        $element2[] = wfDocument::createHtmlElement('img', null, array('src' => $fullname, 'class' => 'img-thumbnail', 'style' => 'width:100%'));
      }else if($this->isExtension(array('pdf', 'yml', 'txt'), $data->get('name'))){
        $element2[] = wfDocument::createHtmlElement('a', $data->get('name'), array('onclick' => "window.open('$fullname')"));
      }
      $element[] = wfDocument::createHtmlElement('p', $element2);
      /**
       * Render.
       */
      wfDocument::renderElement($element);
    }else{
      /**
       * Handle _time param for ajax call to work.
       */
      $form = $this->getYml('element/form.yml');
      if($data->get('btn_upload/text')){
        $form->setById('btn_upload', 'innerHTML', $data->get('btn_upload/text'));
      }
      $id = $data->get('id');
      $form->set('div/innerHTML/form/innerHTML/input_file/attribute/onchange', "plugin_upload_file.$id.validate();");
      $form->set('div/innerHTML/form/innerHTML/input_button/attribute/onclick', "plugin_upload_file.$id.uploadFile();");
      $form->set('div/attribute/id', $id);
      $form->set('div/innerHTML/form/innerHTML/input_file/attribute/id', $id.'_file');
      $form->set('div/innerHTML/btn_select/innerHTML/a/attribute/data_id', $id);
      $form->setById('script', 'innerHTML', "if(typeof PluginUploadFile=='undefined'){console.log('Include widget for upload/file is missing.');};plugin_upload_file.$id = new PluginUploadFile(); plugin_upload_file.$id.setData(".json_encode($data->get()).");");
      $form->setById('max_file_size', 'innerHTML', ($data->get('max_size')/1000000).' mb');
      $form->setById('accept', 'innerHTML', $data->get('accept'));
      wfDocument::renderElement($form->get());
    }
  }
  /**
   * Capture.
   */
  public function widget_capture($data){
    $data = wfArray::get($data, 'data');
    if(!is_array($data)){ $data = wfSettings::getSettingsFromYmlString($data); }
    $data = new PluginWfArray($data);
    if(wfRequest::isPost()){
      /**
       * Uploading file.
       */
      $fileName = $_FILES["file1"]["name"]; // The file name
      $fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
      $data->set('post/fileName', $fileName);
      $data->set('post/fileTmpLoc', $fileTmpLoc);
      $data->set('dir', wfSettings::replaceDir($data->get('dir')));
      if(!wfFilesystem::fileExist($data->get('dir'))){
        throw new Exception("Dir ".$data->get('dir')." does not exist! Can not put file in none existing folder.");
      }
      if($data->get('name')=='*'){
        $target_dir = $data->get('dir').'/'.$_FILES["file1"]["name"];
      }else{
        $target_dir = $data->get('dir').'/'.$data->get('name');
      }
      if (!$fileTmpLoc) { // if file not chosen
        /**
         * Maybe file size in php.ini?
         */
        exit('5');
      }
      if($_FILES["file1"]["size"] > $data->get('max_size')){
        /**
         * To big file.
         */
        exit('1');
      }
      /**
       * Check file type.
       */
      $validate_type = false;
      if($data->get('file_type') && $data->get('file_type') == $_FILES["file1"]["type"]){
        $validate_type = true;
      }
      if(!$validate_type){
        /**
         * Invalid type.
         */
        exit('2');
      }
      /**
       * Accept.
       */
      $accept = false;
      if( strtolower(substr($fileName, (strlen($fileName)-strlen($data->get('accept'))-1)))  == strtolower('.'.$data->get('accept')) ){
        $accept = true;
      }
      /**
       * We also check jpeg if accept=jpg.
       */
      if(!$accept && strtolower($data->get('accept'))=='jpg'){
        $data->set('accept', 'jpeg');
        if( strtolower(substr($fileName, (strlen($fileName)-strlen($data->get('accept'))-1)))  == strtolower('.'.$data->get('accept')) ){
          $accept = true;
        }
      }
      /**
       * 
       */
      if(!$accept){
        $array = array();
        $array['success'] = '6';
        exit(json_encode($array));
      }
      /**
       * Run method before save.
       * Set $form->set('error/text', 'Some error message...') will stop saving file.
       */
      if($data->get('success/before_save/plugin') && $data->get('success/before_save/method')){
        $data = PluginUploadFile::runCaptureMethod($data->get('success/before_save/plugin'), $data->get('success/before_save/method'), $data);
      }
      /**
       * If error is set in before_save method.
       */
      if($data->get('error')){
        exit(json_encode($data->get('error')));
      }
      /**
       * Copy from temp folder.
       */
      if(move_uploaded_file($fileTmpLoc, $target_dir)){
        /**
         * Run method before save.
         */
        if($data->get('success/after_save/plugin') && $data->get('success/after_save/method')){
          $data = PluginUploadFile::runCaptureMethod($data->get('success/after_save/plugin'), $data->get('success/after_save/method'), $data);
        }
        /**
         * 
         */
        $array = array();
        $array['success'] = '1';
        $array['capture_result'] = $data->get('capture_result');
        exit(json_encode($array));
      } else {
        exit('3');
      }
      exit('4');
    }else{
      /**
       * Delete file.
       */
      $fullname = wfSettings::replaceDir($data->get('dir').'/'.$data->get('name'));
      if(wfFilesystem::fileExist($fullname)){
        wfFilesystem::delete($fullname);
      }
      if($data->get('success/script')){
        exit("...<script>".$data->get('success/script')."</script>");
      }
    }
  }
  /**
   * Capture method caller.
   */
  public static function runCaptureMethod($plugin, $method, $form){
    wfPlugin::includeonce($plugin);
    $obj = wfSettings::getPluginObj($plugin);
    return $obj->$method($form);
  }
  /**
   * 
   */
  private function getYml($file){
    return new PluginWfYml(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/upload/file/'.$file);
  }
  /**
   * Check file extension.
   * @param array $extension
   * @param string $name
   * @return boolean
   */
  private function isExtension($extension, $name){
    foreach ($extension as $key => $value) {
      if(strtolower(substr($name, strlen($name) - strlen($value)-1)) == '.'.strtolower($value)){
        return true;
      }
    }
    return false;
  }
}


