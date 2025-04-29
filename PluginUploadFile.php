<?php
/**
 * 
 * Improvements.
 * 180128: Add param success/before_save to run method before save image.
 * Windows: Has to add account IUSR to temp folder with proper privileges.
 */
class PluginUploadFile{
  private $i18n = null;
  /**
   * 
   */
  function __construct($buto){
    if($buto){
      wfPlugin::includeonce('wf/array');
      wfPlugin::includeonce('wf/yml');
    }
    wfPlugin::enable('i18n/file_to_object');
    /**
     * 
     */
    wfPlugin::includeonce('i18n/translate_v1');
    $this->i18n = new PluginI18nTranslate_v1();
    $this->i18n->path = '/plugin/upload/file/i18n';
  }
  /**
   * 
   */
  public static function widget_include(){
    $element = array();
    wfPlugin::enable('include/js');
    $element[] = wfDocument::createWidget('include/js', 'include', array('src' => '/plugin/upload/file/plugin_upload_file.js'));    
    $element[] = wfDocument::createHtmlElement('script', "var plugin_upload_file = {};");
    $element[] = wfDocument::createWidget('i18n/file_to_object', 'run', array('item' => array(array('folder' => '/plugin/upload/file/i18n', 'object' => 'PluginUploadFile.i18n'))));    
    wfDocument::renderElement($element);
  }
  /**
   * Use this widget to only view or access file outside the upload procedure.
   */
  public function widget_view($data){
    $data = new PluginWfArray($data);
    $data = new PluginWfArray($data->get('data'));
    $data = $this->handle_data($data);
    /**
     * replace
     */
    $data->set('name', wfRequest::replaceString($data->get('name')));
    /**
     * Method before
     */
    $data = $this->handle_method_before($data);
    /**
     * 
     */
    $display_name = $data->get('name');
    if($data->get('display_name')){
      $display_name = $data->get('display_name');
    }
    /**
     * 
     */
    $img_style = 'width:100%';
    if($data->get('img/style')){
      $img_style = $data->get('img/style');
    }
    /**
     * 
     */
    $fullname = wfSettings::replaceDir($data->get('dir').'/'.$data->get('name'));
    $element = array();
    if(!$data->get('app_dir')){
      $exist = wfFilesystem::fileExist($fullname);
    }else{
      $exist = wfFilesystem::fileExist(wfGlobals::getAppDir().$fullname);
    }
    if($exist){
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
        $element[] = wfDocument::createHtmlElement('a', $display_name, array('onclick' => "window.open('$fullname')"));
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
  private function handle_method_before($data){
    if($data->get('method/before')){
      foreach ($data->get('method/before') as $key => $value) {
        $i = new PluginWfArray($value);
        $data = PluginUploadFile::runCaptureMethod($i->get('plugin'), $i->get('method'), $data);
      }
    }
    return $data;
  }
  /**
   * 
   */
  private function getImageSize($file_from_root){
    $temp = getimagesize($file_from_root);
    $data = new PluginWfArray();
    $data->set('width', $temp[0]);
    $data->set('height', $temp[1]);
    $data->set('mime', $temp['mime']);
    return $data->get();
  }
  private function handle_data($data){
    $data->set('id_delete', $data->get('id').'_delete');
    if(!$data->get('dir')){
      $data->set('dir', '[web_dir]'.$data->get('web_dir'));
    }
    return $data;
  }
  public function widget_element($data){
    /**
     * 
     */
    $data = new PluginWfArray($data);
    $data = new PluginWfArray($data->get('data'));
    $data = $this->handle_data($data);
    /**
     * Method before
     */
    $data = $this->handle_method_before($data);
    /**
     * Handle url.
     */
    if(!wfPhpfunc::strstr($data->get('url'), '_time=')){
      if(wfPhpfunc::strstr($data->get('url'), '?')){
        $data->set('url', $data->get('url').'&_time='.wfCrypt::getUid());
      }else{
        $data->set('url', $data->get('url').'?_time='.wfCrypt::getUid());
      }
    }
    /**
     * replace
     */
    $data->set('url', wfRequest::replaceString($data->get('url')));
    $data->set('name', wfRequest::replaceString($data->get('name')));
    /**
     * 
     */
    $fullname = wfSettings::replaceDir($data->get('dir').'/'.$data->get('name'));
    if(!$data->get('app_dir')){
      $exist = wfFilesystem::fileExist($fullname);
    }else{
      $exist = wfFilesystem::fileExist(wfGlobals::getAppDir().$fullname);
    }
    if($exist){
      if($data->get('if_file_exist/element')){
        wfDocument::renderElement($data->get('if_file_exist/element'));
        return null;
      }
      /**
       * 
       */
      $fullname = wfSettings::replaceDir($data->get('web_dir')).'/'.$data->get('name').'?x='.wfCrypt::getUid();
      $data->set('fullname', $fullname);
      $data->set('fullpath', wfGlobals::getAppDir().'/'.wfGlobals::get('web_folder'). wfSettings::replaceDir($data->get('web_dir')) .'/'.$data->get('name'));
      $data->set('image', null);
      if($this->isExtension(array('png', 'jpg', 'gif'), $data->get('name'))){
        $data->set('extension_image', true);
        $data->set('image', $this->getImageSize($data->get('fullpath')));
      }else if($this->isExtension(array('pdf', 'yml', 'txt'), $data->get('name'))){
        $data->set('extension_image', false);
      }
      $data->set('filesize', filesize($data->get('fullpath')));
      /**
       * element
       */
      $temp = wfDocument::getElementFromFolder(__DIR__, __FUNCTION__.'_exist');
      $temp->setByTag($data->get());
      wfDocument::renderElement($temp);
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
    $data = $this->handle_data($data);
    /**
     * replace
     */
    $data->set('url', wfRequest::replaceString($data->get('url')));
    $data->set('name', wfRequest::replaceString($data->get('name')));
    /**
     * 
     */
    $data = $this->handle_method_before($data);
    /**
     * 
     */
    if(wfRequest::isPost()){
      /**
       * Uploading file.
       */
      $fileName = $_FILES["file1"]["name"]; // The file name
      $fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
      $data->set('post/fileName', $fileName);
      $data->set('post/fileTmpLoc', $fileTmpLoc);
      $data->set('dir', wfSettings::replaceDir($data->get('dir')));
      $dir = wfSettings::replaceDir($data->get('dir'));
      if($data->get('app_dir')){
        $dir = wfGlobals::getAppDir().$dir;
      }
      wfFilesystem::createDir($dir);
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
      if( strtolower(wfPhpfunc::substr($fileName, (wfPhpfunc::strlen($fileName)-strlen($data->get('accept'))-1)))  == strtolower('.'.$data->get('accept')) ){
        $accept = true;
      }
      /**
       * We also check jpeg if accept=jpg.
       */
      if(!$accept && strtolower($data->get('accept'))=='jpg'){
        $data->set('accept', 'jpeg');
        if( strtolower(wfPhpfunc::substr($fileName, (wfPhpfunc::strlen($fileName)-strlen($data->get('accept'))-1)))  == strtolower('.'.$data->get('accept')) ){
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
      if($data->get('app_dir')){
        $target_dir = wfGlobals::getAppDir().$target_dir;
      }
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
      if(!$data->get('app_dir')){
        $exist = wfFilesystem::fileExist($fullname);
      }else{
        $exist = wfFilesystem::fileExist(wfGlobals::getAppDir().$fullname);
      }
      if($exist){
        if(!$data->get('app_dir')){
          wfFilesystem::delete($fullname);
        }else{
          wfFilesystem::delete(wfGlobals::getAppDir().$fullname);
        }
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
      if(strtolower(wfPhpfunc::substr($name, wfPhpfunc::strlen($name) - wfPhpfunc::strlen($value)-1)) == '.'.strtolower($value)){
        return true;
      }
    }
    return false;
  }
}


