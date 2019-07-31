# GCS manager for KLIMG

library for ease of uploading to gcs from legacy script
use similar struct and working procedure as existing send_to_server()

## HOW TO USE

1. install the package
    `composer require kly/klimg-gcs` or download the package then include the manual loader `include path/to/package/src/loader.php`

2. intantiate the manager
    `$manager = new UploadManager(getenv('GCS_ENDPOINT'), getenv('GCS_KEY'), getenv('GCS_PUBLIC_PREFIX'));`
    I am using getenv in the example, you could use simple string
    **GCS_ENDPOINT** : Google Cloud Storage Endpoint
    **GCS_KEY** : Google Cloud Storage Key
    **GCS_PUBLIC_PREFIX** : Google Cloud Storage bucket public url prefix

3. add the image intended to be sent and designated path for the bucket side
    `$manager->add(array(/*of files*/ 'images/one.jpg'), 'path/on/the/cloud');`

4. when you finish add all the pics execute the transfer
    `$manager->send()`

5. Inspect the result, check any failure

It all just a simple curl wrapper anyway.


