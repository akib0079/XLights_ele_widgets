<?php
/**
 * Generates upload fixtures for tests/security.sh (no binary files in git):
 *
 *   photo-exif.jpg  – valid 320×240 JPEG carrying an EXIF ImageDescription
 *                     marker, used to prove metadata is stripped on upload.
 *   fake-image.jpg  – PHP source with a .jpg name, must be rejected.
 *
 * Usage: php tests/fixtures/make-fixtures.php <output-dir>
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}
$dir = isset( $argv[1] ) ? rtrim( $argv[1], '/' ) : __DIR__;
if ( ! is_dir( $dir ) && ! mkdir( $dir, 0755, true ) ) {
	fwrite( STDERR, "Cannot create $dir\n" );
	exit( 1 );
}
if ( ! function_exists( 'imagecreatetruecolor' ) ) {
	fwrite( STDERR, "GD extension required\n" );
	exit( 1 );
}

// Plain JPEG.
$img = imagecreatetruecolor( 320, 240 );
imagefill( $img, 0, 0, imagecolorallocate( $img, 201, 138, 63 ) );
imagefilledrectangle( $img, 40, 60, 280, 180, imagecolorallocate( $img, 14, 14, 16 ) );
ob_start();
imagejpeg( $img, null, 85 );
$jpeg = ob_get_clean();

// Minimal little-endian TIFF with one IFD0 entry: ImageDescription (0x010E).
$marker = "XL-EXIF-MARKER-GPS-33.4270N-117.6120W\0";
$tiff   = "II*\0" . pack( 'V', 8 )      // Header, IFD0 at offset 8.
	. pack( 'v', 1 )                    // One entry.
	. pack( 'vvVV', 0x010E, 2, strlen( $marker ), 26 ) // Tag, ASCII, count, value offset.
	. pack( 'V', 0 )                    // No next IFD.
	. $marker;
$app1 = "\xFF\xE1" . pack( 'n', strlen( $tiff ) + 8 ) . "Exif\0\0" . $tiff;

file_put_contents( $dir . '/photo-exif.jpg', substr( $jpeg, 0, 2 ) . $app1 . substr( $jpeg, 2 ) );
file_put_contents( $dir . '/fake-image.jpg', "<?php echo 'pwned'; ?>\n" );

echo "Fixtures written to $dir\n";
