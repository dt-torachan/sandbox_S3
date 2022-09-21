<?php
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\Credentials\Credentials;

require '../../vendor/autoload.php';

$s3params['path'] = 'replace/with/path/to/objects/';
$file_list_s3 = S3FileHundler::getlistObjectsFromS3($s3params);
print_r($file_list_s3,true);


class S3FileHundler {

    private static $bucket;
    private static $s3config;
    
    // replace each parameters before use
    private static function initialize(){
        self::$bucket  = 'replaceWith_Bucket';
        self::$s3config = array(
            'version' => 'latest',
            'region'  => 'replaceWith_Region',
            'credentials' => new Credentials('replaceWith_CredentialsKey', 'replaceWith_CredentialsSecret')
        );
    }

    /**
    * putObject(method of S3Client) returns result which contains ObjectURL,statusCode and so on
    * @param array $s3params must contains key,body,contentType
    * @return void
    * @throws S3Exception
    */
    public static function putObjectToS3($s3params)
    {
        self::initialize();
        $s3 = new S3Client(self::$s3config);

        try {
            // Upload data.
            $s3->putObject([
                'Bucket' => self::$bucket,
                'Key'    => $s3params['key'],
                'Body'   => $s3params['body'],
                'ContentType' => $s3params['contentType'],
            ]);
        } catch (S3Exception $e) {
            print_r($e->getMessage(),true);
        }
    }

    /**
    * @param array $s3params must contains key
    * @return void
    * @throws S3Exception
    */
    public static function deleteObjectFromS3($s3params)
    {
        self::initialize();
        $s3 = new S3Client(self::$s3config);

        try {
            $s3->deleteObject([
                'Bucket' => self::$bucket,
                'Key'    => $s3params['key']
            ]);
        } catch (S3Exception $e) {
            print_r($e->getMessage(),true);
        }
    }

    /**
    * @param array $s3params must contains key
    * @return bool
    * @throws S3Exception
    */
    public static function doesObjectExistInS3($s3params)
    {
        self::initialize();
        $s3 = new S3Client(self::$s3config);

        try {
            $result = $s3->doesObjectExist(
                self::$bucket,
                $s3params['key']
            );
        } catch (S3Exception $e) {
            print_r($e->getMessage(),true);
            return false;
        }
        return $result;
    }

    /**
    * @param array $s3params must contains key,contentType
    * @return bool
    * @throws S3Exception
    */
    public static function getObjectFromS3($s3params)
    {
        self::initialize();
        $s3 = new S3Client(self::$s3config);

        try {
            $result = $s3->getObject([
                'Bucket'                     => self::$bucket,
                'Key'                        => $s3params['key'],
                'ResponseContentType'        => $s3params['contentType'],
            ]);
            print_r($result, true);
        } catch (S3Exception $e) {
            print_r($e->getMessage(),true);
            return false;
        }
        return $result['Body'];
    }
    
    /**
    * @param array $s3params must contains path
    * @return object
    * @throws S3Exception
    */
    public static function getlistObjectsFromS3($s3params)
    {
        self::initialize();
        $s3 = new S3Client(self::$s3config);

        try {
            $objects = $s3->listObjects([
                'Bucket' => self::$bucket,
                'Prefix' => $s3params['path'],
            ])['Contents'];
            print_r($objects, true);
        } catch (S3Exception $e) {
            print_r($e->getMessage(),true);
            return null;
        }
        return $objects;
    }
}