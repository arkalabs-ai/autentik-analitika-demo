<?php
/**
 * Penyiapan situs PRATINJAU di WordPress Playground (tautan demo CMS untuk klien).
 * Tanpa foto orang (tim tampil dengan inisial) dan tanpa data klien. Tidak diindeks mesin pencari.
 */
require '/wordpress/wp-load.php';
wp_set_current_user( 1 );

// 1) Field SCF (diimpor sebagai admin agar definisi field utuh)
if ( function_exists( 'acf_import_field_group' ) ) {
	$json = json_decode( (string) file_get_contents( '/wordpress/wp-content/autentik-scf-fields.json' ), true );
	foreach ( (array) $json as $g ) {
		if ( ! acf_get_field_group( $g['key'] ) ) {
			acf_import_field_group( $g );
		}
	}
}

// 2) Pengaturan situs
$home = get_page_by_path( 'beranda' );
$blog = get_page_by_path( 'artikel' );
update_option( 'blogname', 'Law Firm Autentik Analitika' );
update_option( 'blogdescription', 'Kantor hukum di Medan Baru' );
update_option( 'timezone_string', 'Asia/Jakarta' );
update_option( 'date_format', 'j F Y' );
update_option( 'blog_public', '0' ); // pratinjau: jangan diindeks
update_option( 'show_on_front', 'page' );
if ( $home ) {
	update_option( 'page_on_front', $home->ID );
}
if ( $blog ) {
	update_option( 'page_for_posts', $blog->ID );
}
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
wp_update_user( array( 'ID' => 1, 'display_name' => 'Admin Pratinjau' ) );
foreach ( array( get_page_by_path( 'sample-page' ), get_page_by_path( 'hello-world', OBJECT, 'post' ) ) as $x ) {
	if ( $x ) {
		wp_delete_post( $x->ID, true );
	}
}
flush_rewrite_rules();

// 3) Penanda pratinjau di situs & admin
update_option(
	'autentik_pratinjau',
	'Situs pratinjau untuk peninjauan internal. Data tim, legalitas, dan isi layanan masih menunggu konfirmasi kantor; foto tim akan diganti foto asli.'
);
echo "PRATINJAU SIAP\n";
