<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    //

    protected $table = 'configs';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'keywords',
        'logo',
        'favicon',
        'og_image',
        'og_title',
        'og_description',
        'twitter_card_type',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'script_header',
        'script_body',
        'script_footer',
        'landing_page',
        'theme_client',
        'maintenance_mode',
        // rank
        'silver_rank',
        'gold_rank',
        'platinum_rank',
        'diamond_rank',
        //
        'auto_update_service',
        'auto_update',
        'rate_update_percent',
        // mức tiền lên rank
        'up_silver',
        'up_gold',
        'up_platinum',
        'up_diamond',
        // thông tin nạp
        'transfer_code',
        'recharge_promotion',
        'promotion_min',
        // Trạng thái thanh toán
        'bank_status',
        'usdt_status',
        'binance_status',
        'binance_id',
        'binance_qr',
        'binance_api_key',
        'binance_merchant_id',
        'binance_secret_key',
        'fpayaz_key',
        'fpayaz_secret',
        // support link
        'facebook_link',
        'zalo_link',
        'telegram_link',
        'email_link',
        'footer_text',
        'system_version',
        'developer_name',
        'developer_url',
        // /
        'fee_childpanel',
        // cấu hình tiếp thị liên kết
        'affiliate_percent',
        'affiliate_min',
        'affiliate_max',
        'affiliate_status',
        // mailer
        'mailer_driver',
        'mailer_host',
        'mailer_port',
        'mailer_username',
        'mailer_password',
        'mailer_encryption',
        // telegram
        'tele_bot_username',
        'telegram_webhook_url',
        'tele_bot_token',
        'tele_chat_id',
        //
        'script_header',
        'script_body',
        'script_footer',
        'timezone',
        'currency',
        // bank info
        'mb_account_number',
        'mb_api_key',
        'vcb_account_number',
        'vcb_api_key',
        'acb_account_number',
        'acb_api_key',
        'vtb_account_number',
        'vtb_api_key',
        'ocb_account_number',
        'ocb_username',
        'ocb_password',
        // usdt info
        'usdt_wallet',
        'usdt_wallet_token',
        'usdt_exchange_rate',
        'trc20_status',
        'trc20_wallet',
        'trc20_contract',
        'trongrid_api_key',
        // telegram_bot
        'telegram_bot',
        'telegram_chat_id',
        'telegram_status',
        //
        'discount_usdt',
        // smtp
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_from_name',
        // Google OAuth
        'google_client_id',
        'google_client_secret',
        'google_redirect_uri',
        'google_login_status',
        // notiffication
        'notice_modal',
        'notice_service',
        'notice_recharge',
        'status',
        'last_payment_time',
        'domain_main',
        'domain',
        'status_demo',
        // theme customizer
        'theme_color_primary',
        'theme_color_primary_rgb',
        'theme_color_primary_text',
        'theme_color_link',
        'theme_color_link_hover',
        'theme_color_sidebar',
        'theme_color_sidebar_border',
        'theme_color_menu_item',
        'theme_effect',
        'theme_mode',
        'antiddos_status',
        'mobile_bottom_nav_status',
        'client_nav_style',
        'child_panel_enabled',
        'child_panel_monthly_price',
        'cloudflare_email',
        'cloudflare_global_key',
        'cloudflare_account_id',
        'cloudflare_token',
        'cloudflare_ip_host',
        'hosting_panel_type',
        'cpanel_host',
        'cpanel_username',
        'cpanel_password'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'keywords' => 'string',
        'logo' => 'string',
        'favicon' => 'string',
        'og_image' => 'string',
        'og_title' => 'string',
        'og_description' => 'string',
        'twitter_card_type' => 'string',
        'twitter_title' => 'string',
        'twitter_description' => 'string',
        'twitter_image' => 'string',
        'script_header' => 'string',
        'script_body' => 'string',
        'script_footer' => 'string',
        'landing_page' => 'string',
        'theme_client' => 'string',
        'maintenance_mode' => 'string',
        // mailer
        'mailer_driver' => 'string',
        'mailer_host' => 'string',
        'mailer_port' => 'string',
        'mailer_username' => 'string',
        'mailer_password' => 'string',
        'mailer_encryption' => 'string',

        'tele_chat_id' => 'string',
        'antiddos_status' => 'string',
        'mobile_bottom_nav_status' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getFillable()
    {
        return $this->fillable;
    }
}
