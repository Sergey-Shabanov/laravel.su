<?php

use App\FrameworkVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitApplicationStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function(Blueprint $table) {
            $table->increments('id');
            $table->string("title");
            $table->text("description");
            $table->longText('text');
            $table->string("slug")->unique();
            $table->string("source_article_author")->nullable();
            $table->string("source_article_url")->nullable();
            $table->timestamp("published_at")->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('docs', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("version_id")->index();
            $table->string('page')->index();
            $table->string('title')->nullable();
            $table->longText("text")->nullable();
            $table->string("last_commit")->nullable();
            $table->string("last_original_commit")->nullable();
            $table->string("current_original_commit")->nullable();
            $table->timestamp("last_commit_at")->nullable();
            $table->timestamp("last_original_commit_at")->nullable();
            $table->timestamp("current_original_commit_at")->nullable();
            $table->integer("original_commits_ahead")->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function(Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->text('value');
        });

        Schema::create('versions', function(Blueprint $table) {
            $table->increments('id');
            $table->string("title")->index();
            $table->boolean("is_documented");
            $table->timestamps();
        });

        FrameworkVersion::create(['title'=>'4.2', 'is_documented'=>true]);
        FrameworkVersion::create(['title'=>'5.4', 'is_documented'=>true]);
        FrameworkVersion::create(['title'=>'6.x', 'is_documented'=>false]);

        DB::statement("INSERT INTO `articles` (`id`, `title`, `description`, `text`, `slug`, `source_article_author`, `source_article_url`, `published_at`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 'Как правильно переводить документацию Laravel', 'Хотите помочь с переводом документации ? Отлично ! Вот инструкции, как это сделать правильно.', 'Оригинал англоязычной документации находится по адресу [https://github.com/laravel/docs](https://github.com/laravel/docs).\r\n\r\nПеревод документации на русский находится на гитхабе по адресу [https://github.com/LaravelRUS/docs](https://github.com/LaravelRUS/docs) . Апдейт перевода осуществляется пулл-реквестами в этот репозиторий.\r\n\r\nРедактирование репозитория с переводом может происходить в двух вариантах - внесение незначительных изменений и обновление перевода файла до актуального.\r\n\r\n### Внесение мелких изменений\r\n\r\nЕсли вы заметили опечатку, некрасивый перевод, неподходящее употребление термина - вы можете просто отредактировать файл прямо на гитхабе. Не нужно уметь пользоваться git , гитхаб сам сделает пулл-реквест.\r\nЕсли же вы заметили, что в русской документации отсутствует фича, которая есть в документации англоязычной, вам нужно использовать другой вариант работы.\r\n\r\n### Обновление перевода до актуального\r\n\r\n#### Формат файла перевода\r\n\r\nФайлы русскоязычной документации имеют определенный формат. В начале каждого файла должна быть конструкция следующего вида (обратите внимание, что в середине - пустая строка):\r\n```\r\ngit a49894e56c3ac8b837ba7d8687d94f6010cb1808\r\n\r\n---\r\n```\r\nгде `a49894e56c3ac8b837ba7d8687d94f6010cb1808` - полный номер коммита в англоязычной документации, последнего актуального на момент редактирования для данного файла. Это нужно для того, чтобы понимать, что именно переведено, а что еще нет - чтобы следующий переводчик не просматривал глазами весь файл в поисках изменений, а просто внес то, что ему покажет `git diff`\r\n\r\nИтак, последовательность действий при переводе документации следующая.\r\n\r\n#### Настройка git difftool\r\n\r\nЕсли вы этого не сделали, установите себе инструмент для визуального сравнения разных версий текста. Их существует огромное множество, кросплатформенный бесплатный вариант - KDiff3 [http://sourceforge.net/projects/kdiff3/](http://sourceforge.net/projects/kdiff3/)\r\n\r\nПосле установки, отредактируйте глобальный файл `.gitconfig` , который находится в папке пользователя (для Windows это `C:\\Users\\(username)`) . Добавьте туда следующие строки:\r\n\r\n```\r\n[diff]\r\n    tool = kdiff3\r\n\r\n[merge]\r\n    tool = kdiff3\r\n\r\n[mergetool \"kdiff3\"]\r\n    path = C:/Program Files/KDiff3/kdiff3.exe\r\n    keepBackup = false\r\n    trustExitCode = false\r\n```\r\nгде path - это путь до исполняемого файла kdiff3.\r\n\r\nЕсли у вас стоит другая diff-программа, например Araxis Merge или DiffMerge, то погуглите, как её настроить для гита - \'(program name) difftool gitconfig\'\r\n\r\n#### Получение текста для перевода\r\n\r\nСклонируйте репозиторий оригинальной документации\r\n```\r\ngit clone https://github.com/laravel/docs.git original_docs\r\ncd original_docs\r\ngit checkout master\r\n```\r\nили, если он уже у вас есть, обновите нужную вам ветку \r\n```\r\noriginal_docs> git checkout master\r\noriginal_docs> git reset HEAD --hard\r\noriginal_docs> git pull origin master\r\n```\r\n\r\nНа странице [Прогресс перевода](http://laravel.su/status) посмотрите, какой файл нуждается в переводе и скопируйте соответствующую команду `git difftool xxxxxxx xxxxxxx file.md` , чтобы узнать, что именно нужно переводить\r\n```\r\noriginal_docs> git difftool a06af42 bc291ef controllers.md\r\n```\r\nGit захочет запустить внешнюю diff-программу, настроенную на предыдущем шаге, соглашайтесь.\r\n\r\nВ появившейся программе справа будет старый файл, а слева - новый, цветом отмечены расхождения в версиях. \r\n\r\nВнесите необходимые изменения в файл перевода. Изменения нужно внести **все** ,чтобы переведенный файл полностью соответствовал оригинальному. Нельзя останавливаться на середине и пушить изменения - следующий человек может подумать, что файл уже переведён полностью.\r\n\r\n#### Финальные шаги\r\n\r\n**Обязательно** измените в начале переводимого файла полный номер коммита. Этот номер можно взять на той же странице [прогресса перевода](http://laravel.su/docs/status) в столбце \"Текущий оригинал\".\r\n\r\nЗакоммитьте изменения и пошлите пулл-реквест из гитхаба. Старайтесь делать изменение только одного файла во время одного коммита. ', 'rus-documentation-contribution-guide', NULL, NULL, '2019-12-27 07:47:00', '2019-12-27 07:47:03', '2019-12-27 07:47:07', NULL);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("articles");
        Schema::dropIfExists("docs");
        Schema::dropIfExists("settings");
        Schema::dropIfExists("versions");
    }
}
