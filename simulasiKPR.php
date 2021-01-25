<?php

/**
 *
 * @package     simulasiKPR
 * @author      niznet
 * @license     GPL-2.0+
 *
 * Plugin Name: Simulasi KPR
 * Version:     1.1.0
 * Author:      niznet
 * Text Domain: simulasiKPR
 *
 */

// Block direct access to file
defined('ABSPATH') or die('Tidak diperbolehkan!');

class simulasiKPR
{

    public function __construct()
    {
        // Plugin uninstall hook
        register_uninstall_hook(__FILE__, array('simulasiKPR', 'plugin_uninstall'));

        // Plugin activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'plugin_activate'));
        register_deactivation_hook(__FILE__, array($this, 'plugin_deactivate'));

        add_shortcode('kpr', array($this, 'kpr'));

        add_action('wp_enqueue_scripts', array($this, 'plugin_enqueue_scripts'));
        add_action('customize_register', array($this, 'kpr_customize_register'));
    }

    public static function plugin_uninstall()
    {
        $theme_mods = array(
            'kpr_harga_properti',
            'kpr_jumlah_dp',
            'kpr_persentase_dp',
            'kpr_bunga_fixed',
            'kpr_fixed_year',
            'kpr_floating_rate',
            'kpr_tenor',
            'kpr_persentase_dp_display_toggle',
            'kpr_fixed_year_display_toggle',
            'kpr_floating_rate_display_toggle',
            'kpr_title',
            'kpr_harga_properti_title',
            'kpr_jumlah_dp_title',
            'kpr_persentase_dp_title',
            'kpr_bunga_fixed_title',
            'kpr_fixed_year_title',
            'kpr_floating_rate_title',
            'kpr_tenor_title'
        );

        foreach ($theme_mods as $mod){
            remove_theme_mod($mod);
        }
    }

    public function plugin_activate()
    {
        if (!get_theme_mod('kpr_title')){
            set_theme_mod( 'kpr_title', 'KPR Simulator' );
        }
        if (!get_theme_mod('kpr_harga_properti_title')){
            set_theme_mod( 'kpr_harga_properti_title', 'Harga Properti' );
        }
        if (!get_theme_mod('kpr_jumlah_dp_title')){
            set_theme_mod( 'kpr_jumlah_dp_title', 'Jumlah DP' );
        }
        if (!get_theme_mod('kpr_persentase_dp_title')){
            set_theme_mod( 'kpr_persentase_dp_title', '% DP' );
        }
        if (!get_theme_mod('kpr_bunga_fixed_title')){
            set_theme_mod( 'kpr_bunga_fixed_title', 'Bunga Fixed' );
        }
        if (!get_theme_mod('kpr_fixed_year_title')){
            set_theme_mod( 'kpr_fixed_year_title', 'Fixed Year' );
        }
        if (!get_theme_mod('kpr_floating_rate_title')){
            set_theme_mod( 'kpr_floating_rate_title', 'Floating Rate' );
        }
        if (!get_theme_mod('kpr_tenor_title')){
            set_theme_mod( 'kpr_tenor_title', 'Tenor' );
        }
        if (!get_theme_mod('kpr_harga_properti')){
            set_theme_mod( 'kpr_harga_properti', 500000000 );
        }
        if (!get_theme_mod('kpr_jumlah_dp')){
            set_theme_mod( 'kpr_jumlah_dp', 50000000 );
        }
        if (!get_theme_mod('kpr_persentase_dp')){
            set_theme_mod( 'kpr_persentase_dp', 10 );
        }
        if (!get_theme_mod('kpr_bunga_fixed')){
            set_theme_mod( 'kpr_bunga_fixed', '9.5' ); //bunga fixed selama rentan waktu
        }
        if (!get_theme_mod('kpr_fixed_year')){
            set_theme_mod( 'kpr_fixed_year', 3 );
        }
        if (!get_theme_mod('kpr_floating_rate')){
            set_theme_mod( 'kpr_floating_rate', '12.25' );
        }
        if (!get_theme_mod('kpr_tenor')){
            set_theme_mod( 'kpr_tenor', 15 ); //jangka waktu
        }
        if (!get_theme_mod('kpr_persentase_dp_display_toggle')){
            set_theme_mod( 'kpr_persentase_dp_display_toggle', true );
        }
        if (!get_theme_mod('kpr_fixed_year_display_toggle')){
            set_theme_mod( 'kpr_fixed_year_display_toggle', true );
        }
        if (!get_theme_mod('kpr_floating_rate_display_toggle')){
            set_theme_mod( 'kpr_floating_rate_display_toggle', true );
        }
        if (!get_theme_mod('kpr_persentase_dp_min')){
            set_theme_mod( 'kpr_persentase_dp_min', 1 );
        }
        if (!get_theme_mod('kpr_bunga_fixed_min')){
            set_theme_mod( 'kpr_bunga_fixed_min', 1 );
        }
        if (!get_theme_mod('kpr_fixed_year_min')){
            set_theme_mod( 'kpr_fixed_year_min', 1 );
        }
        if (!get_theme_mod('kpr_floating_rate_min')){
            set_theme_mod( 'kpr_floating_rate_min', 1 );
        }
        if (!get_theme_mod('kpr_tenor_min')){
            set_theme_mod( 'kpr_tenor_min', 1 );
        }
    }

    public function plugin_deactivate()
    {
    }

    public function plugin_enqueue_scripts()
    {
        $plugin_data = get_plugin_data( __FILE__ );
        $plugin_version = $plugin_data['Version'];
        wp_enqueue_style( 'kpr-css', plugin_dir_url( __FILE__ ) . 'assets/kpr.min.css', array(), $plugin_version, 'all' );
        wp_enqueue_script( 'kpr', plugin_dir_url( __FILE__ ) . 'assets/kpr.min.js', array(), $plugin_version, true );
    }

    function kpr($atts, $content = null)
    {
        ob_start();
        ?>
        <div class="kpr container">
            <h2 class="title"><?= get_theme_mod('kpr_title') ?></h2>
            <form method="POST" onsubmit="return hitung()" class="row">
                <div class="kpr-input">
                    <div class="kpr-group">
                        <div class="form-group">
                            <label for="harga_properti"><?= get_theme_mod('kpr_harga_properti_title') ?></label><br>
                            <span class="kpr-input f">
                                Rp. <input type="rp" name="harga_properti" id="harga_properti" value="<?= get_theme_mod( 'kpr_harga_properti' ) ?>" required="required" autofocus>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_dp"><?= get_theme_mod('kpr_jumlah_dp_title') ?></label><br>
                            <span class="kpr-input f">
                                Rp. <input type="rp" name="jumlah_dp" id="jumlah_dp" value="<?= get_theme_mod( 'kpr_jumlah_dp' ) ?>" required="required" autofocus>
                            </span>
                        </div>
                        <?php if (get_theme_mod('kpr_persentase_dp_display_toggle')): ?>
                        <div class="form-group">
                            <label for="persentase_dp"><?= get_theme_mod('kpr_persentase_dp_title') ?></label><br>
                            <span class="kpr-input b">
                                <input type="number" name="persentase_dp" id="persentase_dp" value="<?= get_theme_mod( 'kpr_persentase_dp' ) ?>" step="any" min="<?= get_theme_mod( 'kpr_persentase_dp_min' ) ?>" autofocus> %
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="kpr-group">
                        <div class="form-group">
                            <label for="bunga_fixed"><?= get_theme_mod('kpr_bunga_fixed_title') ?></label><br>
                            <span class="kpr-input b">
                                <input type="number" name="bunga_fixed" id="bunga_fixed" value="<?= get_theme_mod( 'kpr_bunga_fixed' ) ?>" step="any" min="<?= get_theme_mod( 'kpr_bunga_fixed_min' ) ?>" autofocus> %
                            </span>
                        </div>
                        <?php if (get_theme_mod('kpr_fixed_year_display_toggle')): ?>
                        <div class="form-group">
                            <label for="fixed_year"><?= get_theme_mod('kpr_fixed_year_title') ?></label><br>
                            <span class="kpr-input b">
                                <input type="number" name="fixed_year" id="fixed_year" value="<?= get_theme_mod( 'kpr_fixed_year' ) ?>" min="<?= get_theme_mod( 'kpr_fixed_year_min' ) ?>" autofocus> thn
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if (get_theme_mod('kpr_floating_rate_display_toggle')): ?>
                        <div class="form-group">
                            <label for="floating_rate"><?= get_theme_mod('kpr_floating_rate_title') ?></label><br>
                            <span class="kpr-input b">
                                <input type="number" name="floating_rate" id="floating_rate" value="<?= get_theme_mod( 'kpr_floating_rate' ) ?>" step="any" min="<?= get_theme_mod( 'kpr_floating_rate_min' ) ?>" autofocus> %
                            </span>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="tenor"><?= get_theme_mod('kpr_tenor_title') ?></label><br>
                            <span class="kpr-input b">
                                <input type="number" name="tenor" id="tenor" value="<?= get_theme_mod( 'kpr_tenor' ) ?>" min="<?= get_theme_mod( 'kpr_tenor_min' ) ?>" autofocus> thn
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row results">
                    <div class="form-group">
                        <h3>Jumlah Pinjaman</h3>
                        <div class="result">
                            <h4>Jumlah Harga Properti</h4>
                            <p id="result_harga_properti"></p>
                        </div>
                        <div class="result">
                            <h4>Pokok Pinjaman</h4>
                            <p id="result_pokok_pinjaman"></p>
                            <span>Harga Properti - Uang Muka (DP)</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <h3>Angsuran</h3>
                        <div class="result">
                            <h4><span id="fixedYear"></span> Bulan pertama (Perbulan)</h4>
                            <p id="result_angsuran_perbulan"></p>
                            <span>Bunga Fixed <span class="bungafixed"></span>%</span>
                        </div>
                        <?php if (get_theme_mod('kpr_floating_rate_display_toggle')): ?>
                        <div class="result">
                            <h4>Angsuran setelah floating</h4>
                            <p id="result_angsuran_floating"></p>
                            <span>Asumsi Bunga Floating <span class="floatingrate"></span>%</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">HITUNG KPR*</button>
            </form>
            <span class="disclaimer">*Hanya berlaku untuk perhitungan awal. Konfirmasi lebih lanjut bergantung pada otoritas keuangan terkait.</span>
        </div>
        <?php
        if (get_theme_mod('kpr_custom_css')) {

        $output_css = '
            .kpr .title, .kpr-input.f, .kpr-input.b, .kpr-input input, .kpr .results h3 {
                color: '. get_theme_mod('kpr_warna_teks') .';
            }
            .kpr .btn {
                color: '. get_theme_mod('kpr_warna_teks_tombol') .';
            }
            .kpr-input input, .results .form-group {
                border: 1px solid ' . get_theme_mod( 'kpr_warna_border' ) . ';
            }
            .kpr-input.f, .kpr-input.b, .kpr .results h3 {
                background: ' . get_theme_mod('kpr_warna_background') . ';
            }
            .kpr .btn {
                border: 1px solid '. get_theme_mod( 'kpr_warna_tombol_border' ) .';
            }
            .kpr .btn {
                background: '.get_theme_mod('kpr_warna_tombol_background').';
            }
            .kpr .btn:hover {
                color: '.get_theme_mod('kpr_warna_teks_tombol_hover').';
            }
            .kpr .btn:hover {
                background: '.get_theme_mod('kpr_warna_background_tombol_hover').';
            }
        ';
        }
        ?>
        <style type="text/css">
            <?= $output_css ?>
        </style>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    function kpr_customize_register( $wp_customize ){
        $wp_customize->add_section( 'kpr_settings',
            array(
                'title' => __( 'KPR Settings' ),
                'description' => esc_html__( 'Edit pengaturan KPR' ),
                'priority'  => 160,
            )
        );

        // Harga Properti Title
        $wp_customize->add_setting(
            'kpr_title',
            array(
                'default'   => 'KPR Simulator',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control(
            'kpr_title',
            array(
                'label' => __('Judul KPR'),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_title',
                'type'  => 'text'
            )
        );

        // Harga Properti Title
        $wp_customize->add_setting( 'kpr_harga_properti_title',
            array(
                'default'   => 'Harga Properti',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_harga_properti_title',
            array(
                'label' => __( 'Judul Harga Properti' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_harga_properti_title',
                'type'  => 'text'
            )
        );
        // Harga Properti Default
        $wp_customize->add_setting( 'kpr_harga_properti',
            array(
                'default'   => '500000000',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_harga_properti',
            array(
                'label' => __( 'Harga Properti Default' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_harga_properti',
                'type'  => 'text'
            )
        );
        
        // Jumlah DP Title
        $wp_customize->add_setting( 'kpr_jumlah_dp_title',
            array(
                'default'   => 'Jumlah DP',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_jumlah_dp_title',
            array(
                'label' => __( 'Judul Jumlah DP' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_jumlah_dp_title',
                'type'  => 'text'
            )
        );
        // Jumlah DP Default
        $wp_customize->add_setting( 'kpr_jumlah_dp',
            array(
                'default'   => '50000000',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_jumlah_dp',
            array(
                'label' => __( 'Jumlah DP Default' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_jumlah_dp',
                'type'  => 'text'
            )
        );
        
        // % DP Display Toggle
        $wp_customize->add_setting( 'kpr_persentase_dp_display_toggle',
            array(
                'default'   => 1,
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_persentase_dp_display_toggle',
            array(
                'label' => __( 'Munculkan % DP' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_persentase_dp_display_toggle',
                'type'  => 'checkbox'
            )
        );
        // % DP Title
        $wp_customize->add_setting( 'kpr_persentase_dp_title',
            array(
                'default'   => '% DP',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_persentase_dp_title',
            array(
                'label' => __( 'Judul % DP' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_persentase_dp_title',
                'type'  => 'text'
            )
        );
        // % DP Default
        $wp_customize->add_setting( 'kpr_persentase_dp',
            array(
                'default'   => '10',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_persentase_dp',
            array(
                'label' => __( '% DP Default' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_persentase_dp',
                'type'  => 'text'
            )
        );
        // % DP Minimum
        $wp_customize->add_setting( 'kpr_persentase_dp_min',
            array(
                'default'   => '1',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_persentase_dp_min',
            array(
                'label' => __( 'Minimal % DP' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_persentase_dp_min',
                'type'  => 'text'
            )
        );
        
        // Bunga Fixed Title
        $wp_customize->add_setting( 'kpr_bunga_fixed_title',
            array(
                'default'   => 'Bunga Fixed',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_bunga_fixed_title',
            array(
                'label' => __( 'Judul Bunga Fixed' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_bunga_fixed_title',
                'type'  => 'text'
            )
        );
        // Bunga Fixed Default
        $wp_customize->add_setting( 'kpr_bunga_fixed',
            array(
                'default'   => '9.5',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_bunga_fixed',
            array(
                'label' => __( 'Bunga Fixed Default' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_bunga_fixed',
                'type'  => 'text'
            )
        );
        // Floating Rate Minimum
        $wp_customize->add_setting( 'kpr_bunga_fixed_min',
            array(
                'default'   => '1',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_bunga_fixed_min',
            array(
                'label' => __( 'Minimal Bunga Fixed' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_bunga_fixed_min',
                'type'  => 'text'
            )
        );
        
        // Fixed Year Display Toggle
        $wp_customize->add_setting( 'kpr_fixed_year_display_toggle',
            array(
                'default'   => 1,
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_fixed_year_display_toggle',
            array(
                'label' => __( 'Munculkan Fixed Year' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_fixed_year_display_toggle',
                'type'  => 'checkbox'
            )
        );
        // Fixed Year Title
        $wp_customize->add_setting( 'kpr_fixed_year_title',
            array(
                'default'   => 'Fixed Year',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_fixed_year_title',
            array(
                'label' => __( 'Judul Fixed Year' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_fixed_year_title',
                'type'  => 'text'
            )
        );
        // Fixed Year Default
        $wp_customize->add_setting( 'kpr_fixed_year',
            array(
                'default'   => '3',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_fixed_year',
            array(
                'label' => __( 'Fixed Year Default' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_fixed_year',
                'type'  => 'text'
            )
        );
        // Fixed Year Minimum
        $wp_customize->add_setting( 'kpr_fixed_year_min',
            array(
                'default'   => '1',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_fixed_year_min',
            array(
                'label' => __( 'Minimal Fixed Year' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_fixed_year_min',
                'type'  => 'text'
            )
        );
        
        // Floating Rate Display Toggle
        $wp_customize->add_setting( 'kpr_floating_rate_display_toggle',
            array(
                'default'   => 1,
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_floating_rate_display_toggle',
            array(
                'label' => __( 'Munculkan Floating Rate' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_floating_rate_display_toggle',
                'type'  => 'checkbox'
            )
        );
        // Floating Rate Title
        $wp_customize->add_setting( 'kpr_floating_rate_title',
            array(
                'default'   => 'Floating Rate',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_floating_rate_title',
            array(
                'label' => __( 'Judul Floating Rate' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_floating_rate_title',
                'type'  => 'text'
            )
        );
        // Floating Rate Default
        $wp_customize->add_setting( 'kpr_floating_rate',
            array(
                'default'   => '12.25',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_floating_rate',
            array(
                'label' => __( 'Floating Rate Default' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_floating_rate',
                'type'  => 'text'
            )
        );
        // Floating Rate Minimum
        $wp_customize->add_setting( 'kpr_floating_rate_min',
            array(
                'default'   => '1',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_floating_rate_min',
            array(
                'label' => __( 'Minimal Floating Rate' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_floating_rate_min',
                'type'  => 'text'
            )
        );
        
        // Tenor Title
        $wp_customize->add_setting( 'kpr_tenor_title',
            array(
                'default'   => 'Tenor',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_tenor_title',
            array(
                'label' => __( 'Judul Tenor' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_tenor_title',
                'type'  => 'text'
            )
        );
        // Tenor Default
        $wp_customize->add_setting( 'kpr_tenor',
            array(
                'default'   => '15',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_tenor',
            array(
                'label' => __( 'Tenor Default' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_tenor',
                'type'  => 'text'
            )
        );
        // Floating Rate Minimum
        $wp_customize->add_setting( 'kpr_tenor_min',
            array(
                'default'   => '1',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_tenor_min',
            array(
                'label' => __( 'Minimal Tenor' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_tenor_min',
                'type'  => 'text'
            )
        );

        // Nyalakan Custom CSS/Style (Atur style dibawah)
        $wp_customize->add_setting( 'kpr_custom_css',
            array(
                'default'   => 1,
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_custom_css',
            array(
                'label' => __( 'Nyalakan Custom CSS/Style (Atur style dibawah)' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_custom_css',
                'type'  => 'checkbox'
            )
        );

        // Warna Background Input
        $wp_customize->add_setting( 'kpr_background_input',
            array(
                'default'   => '#e3dad4',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_background_input',
            array(
                'label' => __( 'Warna Background Input' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_background_input',
                'type'  => 'color'
            )
        );

        // Warna Teks
        $wp_customize->add_setting( 'kpr_warna_teks',
            array(
                'default'   => '#a08471',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_warna_teks',
            array(
                'label' => __( 'Warna Teks' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_warna_teks',
                'type'  => 'color'
            )
        );

        // Warna Border
        $wp_customize->add_setting( 'kpr_warna_border',
            array(
                'default'   => '#e3dad4',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_warna_border',
            array(
                'label' => __( 'Warna Border' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_warna_border',
                'type'  => 'color'
            )
        );

        // Warna Background
        $wp_customize->add_setting( 'kpr_warna_background',
            array(
                'default'   => '#e3dad4',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_warna_background',
            array(
                'label' => __( 'Warna Background' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_warna_background',
                'type'  => 'color'
            )
        );

        // Warna Border Tombol
        $wp_customize->add_setting( 'kpr_warna_tombol_border',
            array(
                'default'   => '#4d3213',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_warna_tombol_border',
            array(
                'label' => __( 'Warna Border Tombol' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_warna_tombol_border',
                'type'  => 'color'
            )
        );

        // Warna Background Tombol
        $wp_customize->add_setting( 'kpr_warna_tombol_background',
            array(
                'default'   => '#4d3213',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_warna_tombol_background',
            array(
                'label' => __( 'Warna Background Tombol' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_warna_tombol_background',
                'type'  => 'color'
            )
        );

        // Warna Teks Tombol
        $wp_customize->add_setting( 'kpr_warna_teks_tombol',
            array(
                'default'   => '#fff',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_warna_teks_tombol',
            array(
                'label' => __( 'Warna Teks' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_warna_teks_tombol',
                'type'  => 'color'
            )
        );

        // Warna Teks Hover Tombol
        $wp_customize->add_setting( 'kpr_warna_teks_tombol_hover',
            array(
                'default'   => '#4d3213',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_warna_teks_tombol_hover',
            array(
                'label' => __( 'Warna Teks Hover Tombol' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_warna_teks_tombol_hover',
                'type'  => 'color'
            )
        );

        // Warna Teks Hover Tombol
        $wp_customize->add_setting( 'kpr_warna_background_tombol_hover',
            array(
                'default'   => '#fff',
                'transport' => 'refresh',
                'capability' => 'edit_theme_options'
            )
        );
        $wp_customize->add_control( 'kpr_warna_background_tombol_hover',
            array(
                'label' => __( 'Warna Background Hover Tombol' ),
                'section'   => 'kpr_settings',
                'settings'  => 'kpr_warna_background_tombol_hover',
                'type'  => 'color'
            )
        );
    }
}

new simulasiKPR;
