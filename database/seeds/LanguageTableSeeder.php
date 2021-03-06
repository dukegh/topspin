<?php
use Illuminate\Database\Seeder;
use App\Language;

class LanguageTableSeeder extends Seeder {

    public function run()
    {
        DB::table('languages')->delete();

        $language = new Language();
        $language->name = 'English';
        $language->lang_code = 'gb';
        $language->save();

        $language = new Language();
        $language->name = 'Русский';
        $language->lang_code = 'ru';
        $language->save();

    }

}
