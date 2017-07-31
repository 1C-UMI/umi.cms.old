<?php

$FORMS = Array();

$FORMS['vote_block'] = <<<END
<div id="vote">
<form method="post" name="postForm_%id%">
	<table border="0" class="bg_vote" width="100%">
			<tr><td height="15"><img src="/images/bg_vote_top.gif" width="100%" height="15" alt=""></td></tr>
			<tr><td class="h1 red pad_left">%h1%</td></tr>
			<tr><td><table border="0" width="95%" class="bg_vote_w">
						<tr><td><img src="/images/bg_vote_wt.gif" width="100%" height="15" alt=""></td></tr>
							<tr><td class="pad_left vote_text"><b>%text%</b></td></tr>
							<tr><td><img src="/images/bg_vote_wb.gif" width="100%" height="15" alt=""></td></tr>
					</table></td></tr>
			%lines%
			<tr><td class="pad_vote vote_text">%submit%<img src="/images/spacer.gif" width="15" height="1" alt="" border="0"><a href="%link%" class="blue">Результаты </a><img src="/images/spacer.gif" width=8 height=1 alt="" border="0"></td></tr>
			<tr><td height="15"><img src="/images/bg_vote_bot.gif" width="100%" height="15" alt=""></td></tr>
	</table>
</form>
</div>


END;

$FORMS['vote_block_line'] = <<<END

	<input type="radio" name="vote_results" value="%item_id%" />%item_name% <br />

END;

$FORMS['vote_block_submit'] = <<<END

<a href="/" onclick="javscript: cms_vote_postDo('postForm_%id%', 'vote_results', '%vote_not_selected%'); return false;"><img src="/images/submit_vote.gif" width="82" align="absmiddle" height="18" alt="" border="0"></a>


END;

$FORMS['vote_block_line'] = <<<END
	<tr>
		<td class="pad_left vote_text">
			<input type="radio" name="vote_results" value="%item_id%" class="search" id="vote_item_%item_id%">
				<img src="/images/spacer.gif" width=8 height=1 alt="" border="0">
				%item_name%
		</td>
	</tr>
END;


$FORMS['result_block'] = <<<END

<table border="0" class="bg_vote" width="100%">
			<tr><td height="15"><img src="/images/bg_vote_top.gif" width="100%" height="15" alt=""></td></tr>
			<tr><td class="h1 red pad_left">Результаты</td></tr>
			<tr><td><table border="0" width="95%" class="bg_vote_w">
						<tr><td><img src="/images/bg_vote_wt.gif" width="100%" height="15" alt=""></td></tr>
							<tr><td class="pad_left vote_text"><b>%question%</b></td></tr>
							<tr><td><img src="/images/bg_vote_wb.gif" width="100%" height="15" alt=""></td></tr>
					</table></td></tr>
			%lines%
			<tr><td class="pad_left vote_text">
			<br/>
			
			<p><b>Всего голосов: %total_posts%</b></p>
			</td></tr>
			<tr><td class="pad_vote vote_text"><img src="/images/spacer.gif" width="15" height="1" alt="" border="0"><a href="%link%" class="blue">Результаты </a><img src="/images/spacer.gif" width=8 height=1 alt="" border="0"></td></tr>
			<tr><td height="15"><img src="/images/bg_vote_bot.gif" width="100%" height="15" alt=""></td></tr>
	</table>
END;

$FORMS['result_block_line'] = <<<END

<tr>
	<td class="pad_left vote_text">
		%item_name% (%item_result% голосов)
	</td>
</tr>
END;

?>