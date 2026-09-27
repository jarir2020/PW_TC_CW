<?php
/**
 * Plugin Name: Pew Frontend Safety Layer
 * Description: Small frontend safeguards and portal presentation rules that should always load with the theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pew_frontend_safety_layer() {
	if ( ! wp_style_is( 'pew-theme', 'enqueued' ) ) {
		return;
	}

	wp_add_inline_script(
		'pew-theme',
		"(function(){document.addEventListener('click',function(event){var link=event.target.closest('a[href=\\\"#\\\"]');if(link){event.preventDefault();event.stopImmediatePropagation();}},true);}());",
		'before'
	);

	wp_add_inline_style(
		'pew-theme',
		'.dashboard-gate,.dashboard-shell{max-width:920px;margin:0 auto;padding:35px;background:#fff;border:1px solid #e3e9e2;border-radius:20px;box-shadow:0 18px 50px rgba(23,32,37,.06)}.dashboard-gate h2,.dashboard-head h2{margin:8px 0 5px;font-size:32px;letter-spacing:-.06em}.dashboard-gate p,.dashboard-head p{margin-top:0;color:#68757a;font-family:"Hind Siliguri",sans-serif}.dashboard-head{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;padding-bottom:25px;border-bottom:1px solid #dfe5df}.dashboard-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:13px;margin-top:25px}.dashboard-cards a{position:relative;display:flex;flex-direction:column;padding:20px;background:#f1f3ee;border-radius:13px}.dashboard-cards a:hover{background:#fff1e9}.dashboard-cards strong{color:#ee7440;font-size:32px;line-height:1}.dashboard-cards span{margin-top:8px;font-family:"Hind Siliguri",sans-serif;font-size:15px}.dashboard-cards i{position:absolute;right:15px;bottom:13px;color:#ee7440;font-style:normal}.dashboard-message{display:flex;align-items:center;gap:18px;margin-top:25px;padding:19px;color:#4a6258;background:#e7f1e8;border-radius:13px}.dashboard-message h3{margin:0 0 4px;font-family:"Hind Siliguri",sans-serif;font-size:17px}.dashboard-message p{margin:0;font-family:"Hind Siliguri",sans-serif;font-size:13px}@media(max-width:700px){.dashboard-gate,.dashboard-shell{padding:23px}.dashboard-head{align-items:flex-start;flex-direction:column}.dashboard-head h2{font-size:28px}.dashboard-cards{grid-template-columns:1fr}}'
	);
}
add_action( 'wp_enqueue_scripts', 'pew_frontend_safety_layer', 20 );

