"use client";

import React, { useState, useEffect } from 'react';
import { Dropdown, Input, Button } from 'antd';import { message } from '@/lib/antd-message';
import { CheckOutlined, DollarCircleOutlined, GlobalOutlined, SearchOutlined } from '@ant-design/icons';

const LANGUAGES = [
    { code: 'vi', name: 'Tiếng Việt', short: 'VI', flag: 'vn' },
    { code: 'en', name: 'English', short: 'EN', flag: 'gb' },
    { code: 'zh-CN', name: '简体中文', short: 'ZH-CN', flag: 'cn' },
    { code: 'zh-TW', name: '繁體中文', short: 'ZH-TW', flag: 'tw' },
    { code: 'ja', name: '日本語', short: 'JA', flag: 'jp' },
    { code: 'ko', name: '한국어', short: 'KO', flag: 'kr' },
    { code: 'th', name: 'ไทย', short: 'TH', flag: 'th' },
    { code: 'km', name: 'ភាសាខ្មែរ', short: 'KM', flag: 'kh' },
    { code: 'lo', name: 'ລາວ', short: 'LO', flag: 'la' },
    { code: 'ms', name: 'Bahasa Melayu', short: 'MS', flag: 'my' },
    { code: 'id', name: 'Bahasa Indonesia', short: 'ID', flag: 'id' },
    { code: 'tl', name: 'Tagalog', short: 'TL', flag: 'ph' },
    { code: 'hi', name: 'हिन्दी', short: 'HI', flag: 'in' },
    { code: 'ar', name: 'العربية', short: 'AR', flag: 'sa' },
    { code: 'ru', name: 'Русский', short: 'RU', flag: 'ru' },
    { code: 'fr', name: 'Français', short: 'FR', flag: 'fr' },
    { code: 'es', name: 'Español', short: 'ES', flag: 'es' },
    { code: 'de', name: 'Deutsch', short: 'DE', flag: 'de' }
];

type Currency = {
    code: string;
    name?: string;
    symbol?: string;
    rate?: number | string;
    exchange_rate?: number | string;
    currency_rate?: number | string;
    exchangeRate?: number | string;
    value?: number | string;
};

type LanguageSwitcherProps = {
    currencies: Currency[];
    activeCurrency?: string;
    balance?: number | string;
    onCurrencyChange: (code: string) => void;
    onOpen?: () => void;
};

const CURRENCY_FLAGS: Record<string, string> = {
    VND: 'vn',
    USD: 'us',
    EUR: 'eu',
    GBP: 'gb',
    JPY: 'jp',
    KRW: 'kr',
    THB: 'th',
    CNY: 'cn',
};

const CURRENCY_SYMBOLS: Record<string, string> = {
    VND: '₫',
    USD: '$',
    EUR: '€',
    GBP: '£',
    JPY: '¥',
    KRW: '₩',
    THB: '฿',
    CNY: '¥',
};

export default function LanguageSwitcher({
    currencies,
    activeCurrency = 'VND',
    balance = 0,
    onCurrencyChange,
    onOpen,
}: LanguageSwitcherProps) {
    const [language, setLanguage] = useState('vi');
    const [search, setSearch] = useState('');
    const [open, setOpen] = useState(false);
    const [activeTab, setActiveTab] = useState<'language' | 'currency'>('language');

    useEffect(() => {
        const match = document.cookie.match(/googtrans=\/vi\/([a-zA-Z-]+)/);
        if (match && match[1]) {
            setLanguage(match[1] === 'vi' ? 'vi' : match[1]);
        }
    }, []);

    const filteredLanguages = LANGUAGES.filter(l => 
        l.name.toLowerCase().includes(search.toLowerCase()) || 
        l.code.toLowerCase().includes(search.toLowerCase())
    );
    const filteredCurrencies = currencies.filter(currency =>
        currency.code.toLowerCase().includes(search.toLowerCase()) ||
        (currency.name || '').toLowerCase().includes(search.toLowerCase())
    );
    const selectedCurrency = currencies.find(currency => currency.code === activeCurrency);
    const selectedCurrencyRate = Number(
        selectedCurrency?.rate ??
        selectedCurrency?.exchange_rate ??
        selectedCurrency?.currency_rate ??
        selectedCurrency?.exchangeRate ??
        selectedCurrency?.value ??
        1
    );
    const vndCurrency = currencies.find(currency => currency.code === 'VND');
    const vndRate = Number(
        vndCurrency?.rate ??
        vndCurrency?.exchange_rate ??
        vndCurrency?.currency_rate ??
        vndCurrency?.exchangeRate ??
        vndCurrency?.value ??
        1
    );
    const convertedBalance = vndRate > 0
        ? (Number(balance || 0) / vndRate) * selectedCurrencyRate
        : Number(balance || 0);

    const dropdownContent = (
        <div className="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl" style={{ width: 310 }}>
            <div className="grid grid-cols-2 border-b border-gray-100 px-2 pt-2">
                <button
                    type="button"
                    onClick={() => { setActiveTab('language'); setSearch(''); }}
                    className={`flex items-center justify-center gap-2 border-b-2 px-3 py-2 text-sm font-semibold transition-colors ${
                        activeTab === 'language' ? 'border-blue-500 text-blue-500' : 'border-transparent text-gray-500'
                    }`}
                >
                    <GlobalOutlined /> Ngôn ngữ
                </button>
                <button
                    type="button"
                    onClick={() => { setActiveTab('currency'); setSearch(''); }}
                    className={`flex items-center justify-center gap-2 border-b-2 px-3 py-2 text-sm font-semibold transition-colors ${
                        activeTab === 'currency' ? 'border-emerald-500 text-emerald-500' : 'border-transparent text-gray-500'
                    }`}
                >
                    <DollarCircleOutlined /> Tiền tệ
                </button>
            </div>
            <div className="p-2">
                <Input 
                    prefix={<SearchOutlined className="text-gray-400" />} 
                    placeholder={activeTab === 'language' ? 'Tìm kiếm ngôn ngữ...' : 'Tìm kiếm tiền tệ...'}
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className={`rounded-xl ${activeTab === 'currency' ? 'border-emerald-300' : 'border-blue-200'}`}
                />
            </div>
            {activeTab === 'currency' && (
                <div className="border-y border-gray-100 py-3 text-center">
                    <div className="text-[10px] uppercase text-gray-400">Số dư</div>
                    <div className="text-lg font-bold text-emerald-500">
                        {convertedBalance.toLocaleString('vi-VN', { maximumFractionDigits: 5 })}
                        {' '}
                        {selectedCurrency?.symbol || CURRENCY_SYMBOLS[activeCurrency] || activeCurrency}
                    </div>
                    <div className="mt-1 text-[10px] text-gray-400">Giá trị có thể thay đổi khi quy đổi ngoại tệ</div>
                </div>
            )}
            <div className="max-h-[300px] overflow-y-auto p-1">
                {activeTab === 'language' && filteredLanguages.map(lang => (
                    <div 
                        key={lang.code}
                        onClick={() => {
                            setLanguage(lang.code);
                            if (lang.code === 'vi') {
                                document.cookie = "googtrans=/vi/vi; path=/;";
                                document.cookie = "googtrans=/vi/vi; domain=" + window.location.hostname + "; path=/;";
                            } else {
                                document.cookie = `googtrans=/vi/${lang.code}; path=/;`;
                                document.cookie = `googtrans=/vi/${lang.code}; domain=${window.location.hostname}; path=/;`;
                            }
                            
                            message.loading({ content: `Đang chuyển sang ${lang.name}...`, key: 'lang' });
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 800);

                            setOpen(false);
                            setSearch('');
                        }}
                        className={`flex items-center justify-between p-2.5 rounded-lg cursor-pointer transition-colors ${language === lang.code ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50 text-gray-700'}`}
                    >
                        <div className="flex items-center gap-3">
                            <img src={`https://flagcdn.com/w20/${lang.flag}.png`} width="20" alt={lang.flag} className="rounded-sm shadow-sm" />
                            <span className={language === lang.code ? 'font-semibold' : 'font-medium'}>{lang.name}</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <span className="text-xs text-gray-400">{lang.short}</span>
                            {language === lang.code && <CheckOutlined className="text-blue-500 text-sm" />}
                        </div>
                    </div>
                ))}
                {activeTab === 'currency' && filteredCurrencies.map(currency => {
                    const selected = activeCurrency === currency.code;
                    const flag = CURRENCY_FLAGS[currency.code.toUpperCase()] || 'un';
                    return (
                        <div
                            key={currency.code}
                            onClick={() => onCurrencyChange(currency.code)}
                            className={`flex cursor-pointer items-center justify-between rounded-lg p-2.5 transition-colors ${
                                selected ? 'bg-emerald-50 text-emerald-600' : 'text-gray-700 hover:bg-gray-50'
                            }`}
                        >
                            <div className="flex items-center gap-3">
                                <img src={`https://flagcdn.com/w40/${flag}.png`} width="28" alt={currency.code} className="rounded-full shadow-sm" />
                                <div>
                                    <span className="font-bold">{currency.code}</span>
                                    {currency.symbol && <span className="ml-1 text-xs text-gray-400">({currency.symbol})</span>}
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="text-[10px] text-gray-500">{currency.name}</span>
                                {selected && <CheckOutlined className="text-emerald-500" />}
                            </div>
                        </div>
                    );
                })}
                {((activeTab === 'language' && filteredLanguages.length === 0) ||
                  (activeTab === 'currency' && filteredCurrencies.length === 0)) && (
                    <div className="p-4 text-center text-gray-400 text-sm">
                        Không tìm thấy kết quả
                    </div>
                )}
            </div>
        </div>
    );

    const activeLang = LANGUAGES.find(l => l.code === language) || LANGUAGES[0];

    return (
        <Dropdown 
            popupRender={() => dropdownContent} 
            trigger={['click']}
            open={open}
            onOpenChange={(v) => {
                setOpen(v);
                if (v) onOpen?.();
                if (!v) {
                    setSearch('');
                    setActiveTab('language');
                }
            }}
            placement="bottomRight"
        >
            <Button type="text" className="flex h-10 w-10 items-center justify-center rounded-full border border-gray-100 p-0">
                <img src={`https://flagcdn.com/w40/${activeLang.flag}.png`} width="24" height="24" alt="flag" className="h-6 w-6 rounded-full object-cover" />
            </Button>
        </Dropdown>
    );
}
