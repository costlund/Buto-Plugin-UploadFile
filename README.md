# Buto-Plugin-UploadFile
Upload a file.



## View image
Widget to view an image.
```
file_upload_data:
  id: _any_id_
  url: '/_any_/_any_'
  max_size: 5000000
  accept: png
  file_type: 'image/png'
  dir: '[web_dir]/data/theme/[theme]/_any_folder_'
  web_dir: '/data/theme/[theme]/_any_folder_'
  name: test.jpg
  image:
    style: ''
  image_not_exist:
    -
      type: span
      innerHTML: Home
  success:
    script: "alert('Success on upload.');"
```






