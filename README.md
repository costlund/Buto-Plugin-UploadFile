# Buto-Plugin-UploadFile

<ul>
<li>Upload a file. </li>
<li>One could upload to buto_data folder (or other outside application folder). </li>
</ul>

<a name="key_0"></a>

## Settings



<a name="key_1"></a>

## Usage



<a name="key_1_0"></a>

### Widget data

<p>Data.</p>
<pre><code>id: _any_id_
url: '/_page_where_capture_widget_is_/id/[id]'
max_size: 5000000
accept: jpg
file_type: 'image/jpeg'
dir: '[web_dir]/data/theme/[theme]/_any_folder_' (omit if web_dir is set)
web_dir: '/data/theme/[theme]/_any_folder_'
name: '[id].jpg'
display_name: '(Optional: Open file)'
image:
  style: ''
image_not_exist:
  -
    type: span
    innerHTML: Upload
success:
  script: "alert('Success on upload.');"</code></pre>
<p>Method before.</p>
<pre><code>method:
  before:
    -
      plugin: '_my_/_plugin_'
      method: my_method_before</code></pre>
<p>Example of method before.</p>
<pre><code>public function my_method_before($data){
  $data-&gt;set('display_name', 'My custom display name');
  return $data;
}</code></pre>
<p>Add param app_dir as an option to not specify root directory.</p>
<pre><code>dir: '/../buto_data/theme/[theme]/_any_folder_'
app_dir: true</code></pre>

<a name="key_2"></a>

## Pages



<a name="key_3"></a>

## Widgets



<a name="key_3_0"></a>

### widget_capture

<p>Capture file.</p>
<pre><code>type: widget
data:
  plugin: 'upload/file'
  method: capture
  data: yml:/plugin/_folder_/_folder/data.yml</code></pre>

<a name="key_3_1"></a>

### widget_element

<p>View, delete and upload.</p>
<pre><code>type: widget
data:
  plugin: 'upload/file'
  method: element
  data: yml:/plugin/_folder_/_folder/data.yml</code></pre>

<a name="key_3_2"></a>

### widget_include

<p>Include in page head.</p>
<pre><code>type: widget
data:
  plugin: 'upload/file'
  method: include</code></pre>

<a name="key_3_3"></a>

### widget_view

<p>View.</p>
<pre><code>type: widget
data:
  plugin: 'upload/file'
  method: view
  data: yml:/plugin/_folder_/_folder/data.yml</code></pre>

<a name="key_4"></a>

## Event



<a name="key_5"></a>

## Construct



<a name="key_5_0"></a>

### __construct



<a name="key_6"></a>

## Methods



<a name="key_6_0"></a>

### handle_method_before



<a name="key_6_1"></a>

### getImageSize



<a name="key_6_2"></a>

### handle_data



<a name="key_6_3"></a>

### runCaptureMethod



<a name="key_6_4"></a>

### getYml



<a name="key_6_5"></a>

### isExtension



