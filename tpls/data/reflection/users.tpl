<?php

$FORMS = Array();

$FORMS['reflection_block'] = <<<END

%groups%

END;

$FORMS['reflection_group'] = <<<END

	<tr>
		<td colspan="2">
			<br/><br /><b>%title%</b><br /><br />
		</td>
	</tr>

	%fields%

END;


$FORMS['reflection_field_string'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<input type="text" name="%input_name%" value="%value%" class="textinputs" />
		</td>
	</tr>

END;


$FORMS['reflection_field_text'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<textarea name="%input_name%" class="textinputs">%value%</textarea>
		</td>
	</tr>

END;


$FORMS['reflection_field_wysiwyg'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<textarea name="%input_name%" class="textinputs">%value%</textarea>
		</td>
	</tr>

END;

$FORMS['reflection_field_int'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<input type="text" name="%input_name%" value="%value%" class="textinputs" />
		</td>
	</tr>

END;


$FORMS['reflection_field_password'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<input type="password" name="%input_name%" value="" class="textinputs" />
		</td>
	</tr>

	<tr>
		<td>
			Подтверждение:
		</td>

		<td>
			<input type="password" name="%input_name%" value="" class="textinputs" />
		</td>
	</tr>

END;


$FORMS['reflection_field_relation'] = <<<END
	<tr>
		<td>
			%title%:
		</td>

		<td>
			<select name="%input_name%" style="width: 205px" class="textinputs">
				<option />
				%options%
			</select>
		</td>
	</tr>

END;

$FORMS['reflection_field_relation_option'] = <<<END
	<option value="%id%">%name%</option>
END;


$FORMS['reflection_field_relation_option_a'] = <<<END
	<option value="%id%" selected="selected">%name%</option>
END;


$FORMS['reflection_field_multiple_relation'] = <<<END
	<tr>
		<td>
			%title%:
		</td>

		<td>
			<select name="%input_name%" class="textinputs" multiple>
				<option />
				%options%
			</select>
		</td>
	</tr>

END;

$FORMS['reflection_field_multiple_relation_option'] = <<<END
	<option value="%id%">%name%</option>
END;


$FORMS['reflection_field_multiple_relation_option_a'] = <<<END
	<option value="%id%" selected="selected">%name%</option>
END;




$FORMS['reflection_field_img_file'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<input type="file" name="%input_name%" style="width: 204px; height:21px;" />
			%data getPropertyOfObject(%object_id%, '%name%', 'avatar')%
		</td>
	</tr>


END;

?>