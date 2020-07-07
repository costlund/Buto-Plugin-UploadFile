function PluginUploadFile(){
  this.i18n = {'_': 'This is set from widget i18n/file_to_object, run.'};
  var max_size = null;
  var types = null;
  var type = null;
  var data = null;
  var accept = null;
  var self = this;
  this.setData = function(d){
    data = d;
    max_size = data['max_size'];
    types = data['file_types'];
    type = data['file_type'];
    accept = data['accept'];
    $('#'+data.id+' progress')[0].style.display = "none";
    $('#'+data.id+' .status')[0].innerHTML = PluginI18nJson_v1.i18n("Select file", PluginUploadFile.i18n);
    var str = "";
    str += "Upload of max "+(data.max_size/1000000)+" mb file size";
    if(data.file_type){
      str += " (types: "+data.file_type+")";
    }
    str += " (accept: "+data.accept+")";
    str += ".";
    $('#'+data.id+' .total')[0].innerHTML = str;
  }
  this.uploadFile = function(){
    $('#'+data.id+' form')[0].style.display = "none";
    $('#'+data.id+' progress')[0].style.display = "";
    var file = document.getElementById(''+data.id+'_file').files[0];
    var form_data = new FormData();
    form_data.append("file1", file);
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", this.progressHandler, false);
    ajax.addEventListener("load",  this.loadHandler,  false);
    ajax.addEventListener("error", this.errorHandler, false);
    ajax.addEventListener("abort", this.abortHandler, false);
    ajax.open("POST", data['url']);
    ajax.send(form_data);
  }
  this.progressHandler = function(e){
    var file = document.getElementById(''+data.id+'_file').files[0];
    var percent = (e.loaded / e.total) * 100;
    $('#'+data.id+' progress')[0].value = Math.round(percent);
    $('#'+data.id+' .status')[0].innerHTML = Math.round(percent)+"%";
    $('#'+data.id+' .total')[0].innerHTML = "Uploaded "+e.loaded+" bytes of "+e.total+" (name:"+file.name+" type:"+file.type+")";
  }
  this.loadHandler = function(e){
    if(e.target.status=='200'){
      var json = JSON.parse(e.target.responseText.trim());
      if(json.success === '1'){
        if(data.success.script){
          eval(data.success.script);
        }
      }else if(json.success === '6'){
        alert(PluginI18nJson_v1.i18n("Accept failure!", PluginUploadFile.i18n));
        self.setData(data);
      }else{
        if(json.text){
          alert(json.text);
        }else{
          alert(PluginI18nJson_v1.i18n("Error", PluginUploadFile.i18n)+': '+e.target.responseText.trim()+'');
        }
      }
    }else{
      alert(PluginI18nJson_v1.i18n("An error occured, status ?status", PluginUploadFile.i18n, [{'key': '?status', 'value': e.target.status}]));
    }
  }
  this.errorHandler = function(e){
    alert('Upload Failed');
  }
  this.abortHandler = function(e){
    alert('Upload Aborted');
  }
  this.validate = function(){
    btn = $('#'+data.id+' .upload_button')[0];
    btn.disabled = true;
    var file = document.getElementById(''+data.id+'_file').files[0];
    if(file.size > max_size){
      alert(PluginI18nJson_v1.i18n("This file is to large (?file_size)", PluginUploadFile.i18n, [{'key': '?file_size', 'value': file.size}]));
    }else if(type && type != file.type ){
      alert(PluginI18nJson_v1.i18n("This file type is not valid (?file_type)", PluginUploadFile.i18n, [{'key': '?file_type', 'value': file.type}]));
    }else{
      btn.disabled = false;
    }
    if(btn.disabled==false){
      btn.onclick();
    }
  }
}
