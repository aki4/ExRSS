{$message}

<fieldset>
	<legend>Настройки ExRSS</legend>
	<form method="post">
		<p>
			<label for="MOD_EXRSS_TITLE">Название:</label>
			<input id="MOD_EXRSS_TITLE" name="MOD_EXRSS_TITLE" type="text" value="{$MOD_EXRSS_TITLE}" />
		</p>
		<p>
			<label for="MOD_EXRSS_LINK">Ссылка на RSS фид:</label>
			<input id="MOD_EXRSS_LINK" name="MOD_EXRSS_LINK" type="text" value="{$MOD_EXRSS_LINK}" />
		</p>
		<p>
			<label for="MOD_EXRSS_UPTIME">Частота обновления (минут):</label>
			<input id="MOD_EXRSS_UPTIME" name="MOD_EXRSS_UPTIME" type="text" value="{$MOD_EXRSS_UPTIME}" />
		</p>
		<p>
			<label for="MOD_EXRSS_LIMIT">Максимум новостей (0 - все):</label>
			<input id="MOD_EXRSS_LIMIT" name="MOD_EXRSS_LIMIT" type="text" value="{$MOD_EXRSS_LIMIT}" />
		</p>
		<p>
			<label>&nbsp;</label>
			<input id="submit_{$module_name}" name="submit_{$module_name}" type="submit" value="Сохранить" class="button" />
		</p>
	</form>
</fieldset>
