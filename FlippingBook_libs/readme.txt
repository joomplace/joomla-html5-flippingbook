В папке googlesource исходники flipbook google (html5+java+javascript). Для запуска нужно:
1)распаковать содержимое appengine-java-sdk-1.6.6.zip;
2) в \googlesource\war\WEB-INF\appengine-web.xml уже прописан <application>html5flipbook</application>;
3) из коммандной строки запускаем appengine-java-sdk\bin\dev_appserver.cmd war-location (путь к war папке);
4) в браузере http://localhost:8080/populateds?locale=0 логин 'testUsername' пароль 'testPassword';
5) проверяем админка http://localhost:8080/_ah/admin FE http://localhost:8080/


В папке withcuttedjava тот же проект, но по максимуму отлючен Java. Запускается аналогично предыдущему.

В папке onlyphp то, что получилось запустить без google app. Запускается просто под apache. База для него в war.sql.

В архиве com_html5flipbook.zip практически готовый компонент с куда более простым скриптом flipbook.