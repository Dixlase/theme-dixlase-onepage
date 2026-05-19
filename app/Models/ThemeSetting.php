<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
 *
 * Dixlase OnePage is dual-licensed. You may use this file under either:
 *
 *   (a) the GNU General Public License version 3 or later, as published
 *       by the Free Software Foundation; or
 *
 *   (b) a commercial license agreement obtained from exc-D inc.
 *
 * Unless you have entered into a commercial license agreement, this
 * file is governed by the GPL terms below.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Themes\DixlaseOnePage\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ThemeSetting extends Model
{
    /**
     * テーブル名
     */
    protected $table = 'thm_dixlase_onepage_settings';

    /**
     * 複数代入可能な属性
     */
    protected $fillable = [
        'name',
        'value',
    ];

    /**
     * キャッシュキーのプレフィックス
     */
    const CACHE_PREFIX = 'theme_setting_';

    /**
     * キャッシュの有効期限（秒）
     */
    const CACHE_TTL = 3600;

    /**
     * 設定値を取得
     *
     * @param string $name 設定キー
     * @param mixed $default デフォルト値
     * @return mixed
     */
    public static function getValue(string $name, $default = null)
    {
        return Cache::remember(
            self::CACHE_PREFIX . $name,
            self::CACHE_TTL,
            function () use ($name, $default) {
                $setting = self::where('name', $name)->first();
                return $setting ? $setting->value : $default;
            }
        );
    }

    /**
     * 設定値を保存
     *
     * @param string $name 設定キー
     * @param mixed $value 設定値
     * @return void
     */
    public static function setValue(string $name, $value): void
    {
        self::updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        );

        // キャッシュをクリア
        Cache::forget(self::CACHE_PREFIX . $name);
    }

    /**
     * 複数の設定値を一括取得
     *
     * @param array $names 設定キーの配列
     * @return array キー名をキーとした連想配列
     */
    public static function getValues(array $names): array
    {
        $settings = self::whereIn('name', $names)->get();
        
        $result = [];
        foreach ($names as $name) {
            $setting = $settings->firstWhere('name', $name);
            $result[$name] = $setting ? $setting->value : null;
        }
        
        return $result;
    }

    /**
     * 複数の設定値を一括保存
     *
     * @param array $settings キー名をキー、値をバリューとした連想配列
     * @return void
     */
    public static function setValues(array $settings): void
    {
        foreach ($settings as $name => $value) {
            self::setValue($name, $value);
        }
    }

    /**
     * すべての設定をオブジェクト形式で取得
     *
     * @return object
     */
    public static function getAllAsObject(): object
    {
        $settings = self::all();
        $result = new \stdClass();
        
        foreach ($settings as $setting) {
            $result->{$setting->name} = $setting->value;
        }
        
        return $result;
    }

    /**
     * 設定値を削除
     *
     * @param string $name 設定キー
     * @return void
     */
    public static function deleteValue(string $name): void
    {
        self::where('name', $name)->delete();
        Cache::forget(self::CACHE_PREFIX . $name);
    }

    /**
     * すべてのキャッシュをクリア
     *
     * @return void
     */
    public static function clearAllCache(): void
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget(self::CACHE_PREFIX . $setting->name);
        }
    }
}
