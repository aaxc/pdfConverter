<?php
include( 'config.php' );

/**
 * This script is based on the WebKit Engine. It MUST be installed prior of using
 * it. Also remember, that PHP user needs rights to execute CLI script.
 * 
 * Requires to be called with POST or GET data
 * 
 * @author   Dainis Abols <dainis@lursoft.lv>
 * @since    19.02.2014
 * @version  2.0
 */
class ConvertToPdf
{
    private $tmp_folder = TMP_FOLDER;
    private $script_url = SCRIPT_URL;
    private $original_url;
    private $domain;
    public $p_content;
    public $p_type;
    
    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
    
    public function getRequest()
    {
        $_p_content = $_REQUEST['content'];
        if ( !$_p_content ) {
            die( 'Content not found!' );
        } else {
            $this->p_content = $_p_content;
        }
        
        $_p_type = $_REQUEST['type'];
        if ( !$_p_type ) {
            $this->p_type = 'url';
        } else {
            $this->p_type = 'post';
        }
    }
    
    /**
     * @param string $content
     * @param string $type
     */
    public function getData( $content, $type )
    {
        if ( $type != 'url' ) {
            $file = $this->tmp_folder . uniqid() . '.html';
            file_put_contents( $file, $content );
            $this->domain = $content;
            $this->original_url = $file;
        } else {
            $output = file_get_contents( $content );
            $this->domain = $output;
            $this->original_url = $content;
        }
        
    }
    
    /**
     * @return string
     */
    public function convertData()
    {
        $tmp = $this->tmp_folder . uniqid() . '.pdf';
        exec( $this->script_url . ' ' . $this->original_url . ' ' . $tmp );
        
        if( is_file( $tmp ) ) {
            $data = file_get_contents( $tmp );
            unlink( $tmp );
            
            if ( $this->p_type == 'post' ) {
                unlink( $this->original_url );
            }
            
            return $data;
        } else {
            return 'Fail';
        }
        
    }
}

$ctp = new ConvertToPdf();
$ctp->getRequest();
$ctp->getData( $ctp->p_content, $ctp->p_type );

$result = $ctp->convertData();

if ( $result != 'Fail' )
{
    header( "Content-type: application/pdf" );
    header( "Content-Length: " . strlen( $result ) );
    header( "Accept-Ranges: " . strlen( $result ) );
    echo $result;
} else {
    echo $ctp->getDomain();
}