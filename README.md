# Buto-Plugin-UploadFile
Upload a file. 
One could upload to buto_data folder (or other outside application folder). 

## Include Javascript

```
type: widget
data:
  plugin: 'upload/file'
  method: include
```

## Widgets
View, delete and upload.
```
type: widget
data:
  plugin: 'upload/file'
  method: element
  data: yml:/plugin/_folder_/_folder/data.yml
```
Capture file.
```
type: widget
data:
  plugin: 'upload/file'
  method: capture
  data: yml:/plugin/_folder_/_folder/data.yml
```
View.
```
type: widget
data:
  plugin: 'upload/file'
  method: view
  data: yml:/plugin/_folder_/_folder/data.yml
```

## Data
```
id: _any_id_
url: '/_page_where_capture_widget_is_/id/[id]'
max_size: 5000000
accept: jpg
file_type: 'image/jpeg'
dir: '[web_dir]/data/theme/[theme]/_any_folder_'
web_dir: '/data/theme/[theme]/_any_folder_'
name: '[id].jpg'
image:
  style: ''
image_not_exist:
  -
    type: span
    innerHTML: Upload
success:
  script: "alert('Success on upload.');"
```
Method before
```
method:
  before:
    -
      plugin: '_my_/_plugin_'
      method: _my_method_
```

### App dir
Add param app_dir as an option to not specify root directory.
```
dir: '/../buto_data/theme/[theme]/_any_folder_'
app_dir: true
```

### Display name
Add optional param to display instead of file name.
```
display_name: Test file
```

## Usage
Example of usage where image name has to be changed.
Data file is upload_member_image.yml.

### Widget
On a page member_view.yml.
```
type: div
attribute:
  class: col-md-6
innerHTML:
  -
    type: widget
    data:
      plugin: 'upload/file'
      method: element
      data: rs:upload_member_image
```
Code
```
$id = '_my_id_';

$upload_member_image = $this->form_get('upload_member_image'); // Get data in PluginWfYml format.
$upload_member_image->set('name', $id.'.jpg');
$upload_member_image->set('url', $upload_member_image->get('url').'?id='.$id);

$element = $this->element_get('member_view');
$element->setByTag(array('upload_member_image' => $upload_member_image->get()));
wfDocument::renderElement($element->get());
```

### Capture
member_image_capture.yml
```
type: widget
data:
  plugin: 'upload/file'
  method: capture
  data: rs:upload_member_image
```

```
$id = wfRequest::get('id');
/**
 * 
 */
$upload_member_image = $this->form_get('upload_member_image');
$upload_member_image->set('name', $id.'.jpg');
/**
 * 
 */
$element = $this->element_get('member_image_capture');
$element->setByTag(array('upload_member_image' => $upload_member_image->get()));
wfDocument::renderElement($element->get());
```
