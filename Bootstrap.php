<?php

namespace WCM\AstroFields\Examples\MetaBox;

/**
 * Plugin Name: (WCM) AstroFields MetaBox Example
 * Description: MetaBox example plugin
 */

// Composer autoloader
if ( file_exists( __DIR__."/vendor/autoload.php" ) )
	require_once __DIR__."/vendor/autoload.php";


use WCM\AstroFields\Core\Mediators\Entity;

use WCM\AstroFields\Core\Commands\ViewCmd;

use WCM\AstroFields\Security\Commands\SanitizeString;
use WCM\AstroFields\Security\Commands\SanitizeMail;

use WCM\AstroFields\MetaBox\Commands\MetaBox as MetaBoxCmd;
use WCM\AstroFields\MetaBox\Templates\Table as MetaBoxTmpl;

use WCM\AstroFields\PostMeta\Commands\SaveMeta;
use WCM\AstroFields\PostMeta\Commands\DeleteMeta;
use WCM\AstroFields\PostMeta\Receivers\PostMetaValue;

use WCM\AstroFields\Standards\Templates\SelectFieldTmpl;
use WCM\AstroFields\Standards\Templates\TextareaFieldTmpl;
use WCM\AstroFields\HTML5\Templates\EmailFieldTmpl;


### META BOX/POST META
add_action( 'wp_loaded', function()
{
	if ( ! is_admin() )
		return;

	// Commands
	$mail_view = new ViewCmd;
	$mail_view
		->setProvider( new PostMetaValue )
		->setTemplate( new EmailFieldTmpl );

	// Entity: Field
	$mail_field = new Entity( 'wcm_test', array(
		'post',
		'page',
	) );
	// Attach Commands
	$mail_field
		->attach( $mail_view, array(
			'attributes' => array(
				'size'     => 40,
				'class'    => 'foo bar baz',
			),
		) )
		->attach( new SaveMeta )
		->attach( new DeleteMeta )
		->attach( new SanitizeMail );

	// Commands
	$select_view = new ViewCmd;
	$select_view
		->setProvider( new PostMetaValue )
		->setTemplate( new SelectFieldTmpl );

	// Entity: Field
	$select_field = new Entity( 'wcm_select', array(
		'post',
	) );
	// Attach Commands
	$select_field
		->attach( $select_view, array(
			'attributes' => array(
				'size'     => 40,
				'class'    => 'foo bar baz',
			),
			'options' => array(
				''        => '-- select --',
				'bar'     => 'Bar',
				'foo'     => 'Foo',
				'baz'     => 'Baz',
				'dragons' => 'Dragons',
			),
		) )
		->attach( new DeleteMeta )
		->attach( new SaveMeta )
		->attach( new SanitizeString );

	// Commands
	$textarea_view = new ViewCmd;
	$textarea_view
		->setProvider( new PostMetaValue )
		->setTemplate( new TextareaFieldTmpl );

	// Entity: Field
	$textarea_field = new Entity( 'wcm_textarea', array(
		'post',
	) );
	// Attach Commands
	$textarea_field
		->attach( $textarea_view, array(
			'attributes' => array(
				'class' => 'attachmentlinks',
				'rows'  => 5,
				'cols'  => 40,
			),
		) )
		->attach( new DeleteMeta )
		->attach( new SaveMeta )
		->attach( new SanitizeString );

	// Command: MetaBox
	$meta_box_cmd = new MetaBoxCmd( 'Test Box' );
	$meta_box_cmd
		->attach( $select_field, 10 )
		->attach( $textarea_field, 20 )
		->attach( $mail_field, 30 )
		->setTemplate( new MetaBoxTmpl );
	// Entity: MetaBox
	$meta_box = new Entity( 'wcm_meta_box', array(
		'post',
		'page',
	) );
	$meta_box->attach( $meta_box_cmd );
} );