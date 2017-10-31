function PluginUploadFile(){
  var max_size = null;
  //var types = ['application/pdf', 'image/jpeg', 'image/png'];
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
    $('#'+data.id+' .status')[0].innerHTML = "Select file";
    var str = "";
    str += "Upload of max "+(data.max_size/1000000)+" mb file size";
//    if(data.file_types){
//      str += " (types: "+data.file_types.toString().replace(',', ', ')+")";
//    }
    if(data.file_type){
      str += " (types: "+data.file_type+")";
    }
    //str += " (accept: "+data.accept.toString().replace(',', ', ')+")";
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
        alert('Accept failure!');
        self.setData(data);
      }else{
        alert('Error: '+e.target.responseText.trim()+'');
      }
    }else{
      alert('An error occured, status '+e.target.status+'.')          
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
      alert('This file is to large ('+file.size+').');
//    }else if(types && types.indexOf(file.type)==-1){
//      alert('This file type is not valid ('+file.type+').');
    }else if(type && type != file.type ){
      alert('This file type is not valid ('+file.type+').');
    }else{
      btn.disabled = false;
    }
    if(btn.disabled==false){
      btn.onclick();
    }
  }
}
//var plugin_upload_file = new PluginUploadFile();
