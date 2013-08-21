<?php
/***************************************
* www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/core/aiml/replace_tomakesafe.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the functions to encode/decode 
*          potentially difficult characters  
***************************************/

/**
 * function entity_replace()
 * A function to replace htmlentity items with a string substitute 
 * So that nothing is broken during parsing/interpreting
 * @param  string $whichway - 'decode' or 'encode'
 * @param  sring $text - the text to decode or encode
 * @return the en/decoded string
**/
function entity_replace($whichway,$text){
	runDebug( __FILE__, __FUNCTION__, __LINE__, "$whichway",4);

	//array of the symbols to be encoded decoded
	$html_entity_symbols =array(

	
		';-)'=>'smiley_space_wink',
		';-)'=>'smiley_wink_1a',
		';)'=>'smiley_wink_2a',
		';-('=>'smiley_wink_3a',
		';('=>'smiley_wink_4a',
		':('=>'smiley_wink_5a',
		':-)'=>'smiley_wink_6a',
		':-)'=>'smiley_wink_7a',
		':)'=>'smiley_wink_8a',
		':-('=>'smiley_wink_9a',
		':)'=>'smiley_wink_10a',		
		';-\)'=>'smiley_wink',
		';-\)'=>'smiley_wink_1',
		';\)'=>'smiley_wink_2',
		';-\('=>'smiley_wink_3',
		';\('=>'smiley_wink_4',
		':\('=>'smiley_wink_5',
		':-\)'=>'smiley_wink_6',
		':-\)'=>'smiley_wink_7',
		':\)'=>'smiley_wink_8',
		':-\('=>'smiley_wink_9',
		':\)'=>'smiley_wink_10',
		'('=>'open_bracket',
		')'=>'close_bracket',
		'&nbsp;'=>'htmlentity_no-break_space',
		'&iexcl;'=>'htmlentity_inverted_exclamation_mark',
		'&cent;'=>'htmlentity_cent_sign',
		'&pound;'=>'htmlentity_pound_sterling_sign',
		'&curren;'=>'htmlentity_general_currency_sign',
		'&yen;'=>'htmlentity_yen_sign',
		'&brvbar;'=>'htmlentity_broken_vertical_bar',
		'&sect;'=>'htmlentity_section_sign',
		'&uml;'=>'htmlentity_umlaut_dieresis',
		'&copy;'=>'htmlentity_copyright_sign',
		'&ordf;'=>'htmlentity_ordinal_indicator_feminine',
		'&laquo;'=>'htmlentity_angle_quotation_mark_left',
		'&not;'=>'htmlentity_not_sign',
		'&shy;'=>'htmlentity_soft_hyphen',
		'&reg;'=>'htmlentity_registered_sign',
		'&macr;'=>'htmlentity_macron',
		'&deg;'=>'htmlentity_degree_sign',
		'&plusmn;'=>'htmlentity_plus-or-minus_sign',
		'&sup2;'=>'htmlentity_superscript_two',
		'&sup3;'=>'htmlentity_superscript_three',
		'&acute;'=>'htmlentity_acute_accent',
		'&micro;'=>'htmlentity_micro_sign',
		'&para;'=>'htmlentity_pilcrow_paragraph_sign',
		'&middot;'=>'htmlentity_middle_dot',
		'&cedil;'=>'htmlentity_cedilla',
		'&sup1;'=>'htmlentity_superscript_one',
		'&ordm;'=>'htmlentity_ordinal_indicator_masculine',
		'&raquo;'=>'htmlentity_angle_quotation_mark_right',
		'&frac14;'=>'htmlentity_fraction_one-quarter',
		'&frac12;'=>'htmlentity_fraction_one-half',
		'&frac34;'=>'htmlentity_fraction_three-quarters',
		'&iquest;'=>'htmlentity_inverted_question_mark',
		'&times;'=>'htmlentity_multiply_sign',
		'&divide;'=>'htmlentity_divide_sign',
		'&quot;'=>'htmlentity_quotation_mark_apl_quote',
		'&amp;'=>'htmlentity_ampersand',
		'&lt;'=>'htmlentity_less-than_sign',
		'&gt;'=>'htmlentity_greater-than_sign',
		'&bull;'=>'htmlentity_bullet_black_small_circle',
		'&hellip;'=>'htmlentity_horizontal_ellipsis_three_dot_leader',
		'&prime;'=>'htmlentity_prime_minutes_feet',
		'&Prime;'=>'htmlentity_double_prime_seconds_inches',
		'&oline;'=>'htmlentity_overline_spacing_overscore',
		'&frasl;'=>'htmlentity_fraction_slash',
		'&weierp;'=>'htmlentity_script_capital_P_Weierstrass_p',
		'&image;'=>'htmlentity_blackletter_capital_I_imaginary_part',
		'&real;'=>'htmlentity_blackletter_capital_R_real_part_symbol',
		'&trade;'=>'htmlentity_trade_mark_sign',
		'&alefsym;'=>'htmlentity_alef_symbol_first_transfinite_cardinal',
		'&larr;'=>'htmlentity_leftwards_arrow',
		'&uarr;'=>'htmlentity_upwards_arrow',
		'&rarr;'=>'htmlentity_rightwards_arrow',
		'&darr;'=>'htmlentity_downwards_arrow',
		'&harr;'=>'htmlentity_left_right_arrow',
		'&crarr;'=>'htmlentity_downwards_arrow_with_corner_leftwards_carriage_return',
		'&lArr;'=>'htmlentity_leftwards_double_arrow',
		'&uArr;'=>'htmlentity_upwards_double_arrow',
		'&rArr;'=>'htmlentity_rightwards_double_arrow',
		'&dArr;'=>'htmlentity_downwards_double_arrow',
		'&hArr;'=>'htmlentity_left_right_double_arrow',
		'&forall;'=>'htmlentity_for_all',
		'&part;'=>'htmlentity_partial_differential',
		'&exist;'=>'htmlentity_there_exists',
		'&empty;'=>'htmlentity_empty_set_null_set_diameter',
		'&nabla;'=>'htmlentity_nabla_backward_difference',
		'&isin;'=>'htmlentity_element_of',
		'&notin;'=>'htmlentity_not_an_element_of',
		'&ni;'=>'htmlentity_contains_as_member',
		'&prod;'=>'htmlentity_n-ary_product_product_sign',
		'&sum;'=>'htmlentity_n-ary_sumation',
		'&minus;'=>'htmlentity_minus_sign',
		'&lowast;'=>'htmlentity_asterisk_operator',
		'&radic;'=>'htmlentity_square_root_radical_sign',
		'&prop;'=>'htmlentity_proportional_to',
		'&infin;'=>'htmlentity_infinity',
		'&ang;'=>'htmlentity_angle',
		'&and;'=>'htmlentity_logical_and_wedge',
		'&or;'=>'htmlentity_logical_or_vee',
		'&cap;'=>'htmlentity_intersection_cap',
		'&cup;'=>'htmlentity_union_cup',
		'&int;'=>'htmlentity_integral',
		'&there4;'=>'htmlentity_therefore',
		'&sim;'=>'htmlentity_tilde_operator_varies_with_similar_to',
		'&cong;'=>'htmlentity_approximately_equal_to',
		'&asymp;'=>'htmlentity_almost_equal_to_asymptotic_to',
		'&ne;'=>'htmlentity_not_equal_to',
		'&equiv;'=>'htmlentity_identical_to',
		'&le;'=>'htmlentity_less-than_or_equal_to',
		'&ge;'=>'htmlentity_greater-than_or_equal_to',
		'&sub;'=>'htmlentity_subset_of',
		'&sup;'=>'htmlentity_superset_of',
		'&nsub;'=>'htmlentity_not_a_subset_of',
		'&sube;'=>'htmlentity_subset_of_or_equal_to',
		'&supe;'=>'htmlentity_superset_of_or_equal_to',
		'&oplus;'=>'htmlentity_circled_plus_direct_sum',
		'&otimes;'=>'htmlentity_circled_times_vector_product',
		'&perp;'=>'htmlentity_up_tack_orthogonal_to_perpendicular',
		'&sdot;'=>'htmlentity_dot_operator',
		'&lceil;'=>'htmlentity_left_ceiling_apl_upstile',
		'&rceil;'=>'htmlentity_right_ceiling',
		'&lfloor;'=>'htmlentity_left_floor_apl_downstile',
		'&rfloor;'=>'htmlentity_right_floor',
		'&lang;'=>'htmlentity_left-pointing_angle_bracket_bra',
		'&rang;'=>'htmlentity_right-pointing_angle_bracket_ket',
		'&clubs;'=>'htmlentity_black_club_suit_shamrock',
		'&hearts;'=>'htmlentity_black_heart_suit_valentine',
		'&diams;'=>'htmlentity_black_diamond_suit',
		'&ensp;'=>'htmlentity_en_space',
		'&emsp;'=>'htmlentity_em_space',
		'&thinsp;'=>'htmlentity_thin_space',
		'&zwnj;'=>'htmlentity_zero_width_non-joiner',
		'&zwj;'=>'htmlentity_zero_width_joiner',
		'&lrm;'=>'htmlentity_left-to-right_mark',
		'&rlm;'=>'htmlentity_right-to-left_mark',
		'&ndash;'=>'htmlentity_en_dash',
		'&mdash;'=>'htmlentity_em_dash',
		'&lsquo;'=>'htmlentity_left_single_quotation_mark',
		'&rsquo;'=>'htmlentity_right_single_quotation_mark',
		'&sbquo;'=>'htmlentity_single_low-9_quotation_mark',
		'&ldquo;'=>'htmlentity_left_double_quotation_mark',
		'&rdquo;'=>'htmlentity_right_double_quotation_mark',
		'&bdquo;'=>'htmlentity_double_low-9_quotation_mark',
		'&dagger;'=>'htmlentity_dagger',
		'&Dagger;'=>'htmlentity_double_dagger',
		'&permil;'=>'htmlentity_per_mille_sign');

		
	
		//number of replacements to check
		$total_replacements = count($html_entity_symbols)-1;
		//get the keys from the array
		$keys = array_keys($html_entity_symbols);
		//get the values from the array
		$values = array_values($html_entity_symbols);

		//do the replacement	
		for($i=0;$i<=$total_replacements;$i++){
			if($whichway=='encode'){
				$text = str_replace($keys[$i],$values[$i],$text);
			}
			else{
				$text = str_replace($values[$i],$keys[$i],$text);
			}
		}

		
	return $text;
}

/**
 * function foreignchar_replace()
 * A function to replace foreign characters with a string substitute 
 * So that nothing is broken during parsing/interpreting
 * @param  string $whichway - 'decode' or 'encode'
 * @param  sring $text - the text to decode or encode
 * @return the en/decoded string
**/
function foreignchar_replace($whichway,$text){
	runDebug( __FILE__, __FUNCTION__, __LINE__, "$whichway",4);
	
	$foreign_char_array = array(
		'&Scaron;'=>'htmlentity_foreign_big_S_caron',
		'&scaron;'=>'htmlentity_foreign_small_s_caron',
		'&Agrave;'=>'htmlentity_foreign_big_A_grave',
		'&Aacute;'=>'htmlentity_foreign_small_a_acute',
		'&Acirc;'=>'htmlentity_foreign_big_A_circ',
		'&Atilde;'=>'htmlentity_foreign_big_A_tilde',
		'&Auml;'=>'htmlentity_foreign_big__A_uml',
		'&Aring;'=>'htmlentity_foreign_big_A_ring',
		'&AElig;'=>'htmlentity_foreign_big_AE_lig',
		'&Ccedil;'=>'htmlentity_foreign_big__C_cedil',
		'&Egrave;'=>'htmlentity_foreign_big_E_grave',
		'&Eacute;'=>'htmlentity_foreign_big_E_acute',
		'&Ecirc;'=>'htmlentity_foreign_big_E_circ',
		'&Euml;'=>'htmlentity_foreign_big_E_uml',
		'&Igrave;'=>'htmlentity_foreign_big_I_grave',
		'&Iacute;'=>'htmlentity_foreign_big_I_acute',
		'&Icirc;'=>'htmlentity_foreign_big_I_circ',
		'&Iuml;'=>'htmlentity_foreign_big_I_uml',
		'&Ntilde;'=>'htmlentity_foreign_big_N_tilde',
		'&Ograve;'=>'htmlentity_foreign_big_O_grave',
		'&Oacute;'=>'htmlentity_foreign_big_O_acuter',
		'&Ocirc;'=>'htmlentity_foreign_big_O_circ',
		'&Otilde;'=>'htmlentity_foreign_big_O_tilde',
		'&Ouml;'=>'htmlentity_foreign_big_O_uml',
		'&Oslash;'=>'htmlentity_foreign_big_O_slash',
		'&Ugrave;'=>'htmlentity_foreign_big_U_grave',
		'&Uacute;'=>'htmlentity_foreign_big_U_acute',
		'&Ucirc;'=>'htmlentity_foreign_big_U_circ',
		'&Uuml;'=>'htmlentity_foreign_big_U_uml',
		'&Yacute;'=>'htmlentity_foreign_big_Y_acute',
		'&THORN;'=>'htmlentity_foreign_big_THORN',
		//'&Yuml;'=>'htmlentity_foreign_big_Y_uml',
		'&szlig;'=>'htmlentity_foreign_small_sz_lig',
		'&agrave;'=>'htmlentity_foreign_small_a_grave',
		'&aacute;'=>'htmlentity_foreign_small_a_acute',
		'&acirc;'=>'htmlentity_foreign_small_a_circ',
		'&atilde;'=>'htmlentity_foreign_small_a_tilde',
		'&auml;'=>'htmlentity_foreign_small_a_uml',
		'&aring;'=>'htmlentity_foreign_small_a_ring',
		'&aelig;'=>'htmlentity_foreign_small_ae_lig',
		'&ccedil;'=>'htmlentity_foreign_small_c_cedil',
		'&egrave;'=>'htmlentity_foreign_small_e_grave',
		'&eacute;'=>'htmlentity_foreign_small_e_acute',
		'&ecirc;'=>'htmlentity_foreign_small_e_circ',
		'&euml;'=>'htmlentity_foreign_small_e_uml',
		'&igrave;'=>'htmlentity_foreign_small_i_grave',
		'&iacute;'=>'htmlentity_foreign_small_i_acute',
		'&icirc;'=>'htmlentity_foreign_small_i_circ',
		'&iuml;'=>'htmlentity_foreign_small_i_uml',
		'&eth;'=>'htmlentity_foreign_small_e_th',
		'&ntilde;'=>'htmlentity_foreign_small_n_tilde',
		'&ograve;'=>'htmlentity_foreign_small_o_grave',
		'&oacute;'=>'htmlentity_foreign_small_o_acute',
		'&ocirc;'=>'htmlentity_foreign_small_o_circ',
		'&otilde;'=>'htmlentity_foreign_small_o_tilde',
		'&ouml;'=>'htmlentity_foreign_small_o_uml',
		'&oslash;'=>'htmlentity_foreign_small_o_slash',
		'&ugrave;'=>'htmlentity_foreign_small_u_grave',
		'&uacute;'=>'htmlentity_foreign_small_u_acute',
		'&ucirc;'=>'htmlentity_foreign_small_u_circ',
		'&yacute;'=>'htmlentity_foreign_small_y_acute',
		'&thorn;'=>'htmlentity_foreign_small_thorn',
		//'&yuml;'=>'htmlentity_foreign_small_y_uml',	
		'&Alpha;'=>'htmlentity_greek_capital_letter_alpha',
		'&Beta;'=>'htmlentity_greek_capital_letter_beta',
		'&Gamma;'=>'htmlentity_greek_capital_letter_gamma',
		'&Delta;'=>'htmlentity_greek_capital_letter_delta',
		'&Epsilon;'=>'htmlentity_greek_capital_letter_epsilon',
		'&Zeta;'=>'htmlentity_greek_capital_letter_zeta',
		'&Eta;'=>'htmlentity_greek_capital_letter_eta',
		'&Theta;'=>'htmlentity_greek_capital_letter_theta',
		'&Iota;'=>'htmlentity_greek_capital_letter_iota',
		'&Kappa;'=>'htmlentity_greek_capital_letter_kappa',
		'&Lambda;'=>'htmlentity_greek_capital_letter_lambda',
		'&Mu;'=>'htmlentity_greek_capital_letter_mu',
		'&Nu;'=>'htmlentity_greek_capital_letter_nu',
		'&Xi;'=>'htmlentity_greek_capital_letter_xi',
		'&Omicron;'=>'htmlentity_greek_capital_letter_omicron',
		'&Pi;'=>'htmlentity_greek_capital_letter_pi',
		'&Rho;'=>'htmlentity_greek_capital_letter_rho',
		'&Sigma;'=>'htmlentity_greek_capital_letter_sigma',
		'&Tau;'=>'htmlentity_greek_capital_letter_tau',
		'&Upsilon;'=>'htmlentity_greek_capital_letter_upsilon',
		'&Phi;'=>'htmlentity_greek_capital_letter_phi',
		'&Chi;'=>'htmlentity_greek_capital_letter_chi',
		'&Psi;'=>'htmlentity_greek_capital_letter_psi',
		'&Omega;'=>'htmlentity_greek_capital_letter_omega',
		'&alpha;'=>'htmlentity_greek_small_letter_alpha',
		'&beta;'=>'htmlentity_greek_small_letter_beta',
		'&gamma;'=>'htmlentity_greek_small_letter_gamma',
		'&delta;'=>'htmlentity_greek_small_letter_delta',
		'&epsilon;'=>'htmlentity_greek_small_letter_epsilon',
		'&zeta;'=>'htmlentity_greek_small_letter_zeta',
		'&eta;'=>'htmlentity_greek_small_letter_eta',
		'&theta;'=>'htmlentity_greek_small_letter_theta',
		'&iota;'=>'htmlentity_greek_small_letter_iota',
		'&kappa;'=>'htmlentity_greek_small_letter_kappa',
		'&lambda;'=>'htmlentity_greek_small_letter_lambda',
		'&mu;'=>'htmlentity_greek_small_letter_mu',
		'&nu;'=>'htmlentity_greek_small_letter_nu',
		'&xi;'=>'htmlentity_greek_small_letter_xi',
		'&omicron;'=>'htmlentity_greek_small_letter_omicron',
		'&pi;'=>'htmlentity_greek_small_letter_pi',
		'&rho;'=>'htmlentity_greek_small_letter_rho',
		'&sigmaf;'=>'htmlentity_greek_small_letter_final_sigma',
		'&sigma;'=>'htmlentity_greek_small_letter_sigma',
		'&tau;'=>'htmlentity_greek_small_letter_tau',
		'&upsilon;'=>'htmlentity_greek_small_letter_upsilon',
		'&phi;'=>'htmlentity_greek_small_letter_phi',
		'&chi;'=>'htmlentity_greek_small_letter_chi',
		'&psi;'=>'htmlentity_greek_small_letter_psi',
		'&omega;'=>'htmlentity_greek_small_letter_omega',
		'&thetasym;'=>'htmlentity_greek_small_letter_theta_symbol',
		'&upsih;'=>'htmlentity_greek_upsilon_with_hook_symbol',
		'&piv;'=>'htmlentity_greek_pi_symbol',
		'&Agrave;'=>'htmlentity_capital_A_grave_accent',
		'&Aacute;'=>'htmlentity_capital_A_acute_accent',
		'&Acirc;'=>'htmlentity_capital_A_circumflex_accent',
		'&Atilde;'=>'htmlentity_capital_A_tilde',
		'&Auml;'=>'htmlentity_capital_A_dieresis_or_umlaut_mark',
		'&Aring;'=>'htmlentity_capital_A_ring',
		'&AElig;'=>'htmlentity_capital_AE_diphthong_ligature',
		'&Ccedil;'=>'htmlentity_capital_C_cedilla',
		'&Egrave;'=>'htmlentity_capital_E_grave_accent',
		'&Eacute;'=>'htmlentity_capital_E_acute_accent',
		'&Ecirc;'=>'htmlentity_capital_E_circumflex_accent',
		'&Euml;'=>'htmlentity_capital_E_dieresis_or_umlaut_mark',
		'&Igrave;'=>'htmlentity_capital_I_grave_accent',
		'&Iacute;'=>'htmlentity_capital_I_acute_accent',
		'&Icirc;'=>'htmlentity_capital_I_circumflex_accent',
		'&Iuml;'=>'htmlentity_capital_I_dieresis_or_umlaut_mark',
		'&ETH;'=>'htmlentity_capital_Eth_Icelandic',
		'&Ntilde;'=>'htmlentity_capital_N_tilde',
		'&Ograve;'=>'htmlentity_capital_O_grave_accent',
		'&Oacute;'=>'htmlentity_capital_O_acute_accent',
		'&Ocirc;'=>'htmlentity_capital_O_circumflex_accent',
		'&Otilde;'=>'htmlentity_capital_O_tilde',
		
		'&Ouml;'=>'htmlentity_capital_O_dieresis_or_umlaut_mark',
		'&Oslash;'=>'htmlentity_capital_O_slash',
		'&Ugrave;'=>'htmlentity_capital_U_grave_accent',
		'&Uacute;'=>'htmlentity_capital_U_acute_accent',
		'&Ucirc;'=>'htmlentity_capital_U_circumflex_accent',
		'&Uuml;'=>'htmlentity_capital_U_dieresis_or_umlaut_mark',
		'&Yacute;'=>'htmlentity_capital_Y_acute_accent',
		'&THORN;'=>'htmlentity_capital_THORN_Icelandic',
		'&szlig;'=>'htmlentity_small_sharp_s_German_sz_ligature',
		'&agrave;'=>'htmlentity_small_a_grave_accent',
		'&aacute;'=>'htmlentity_small_a_acute_accent',
		'&acirc;'=>'htmlentity_small_a_circumflex_accent',
		'&atilde;'=>'htmlentity_small_a_tilde',
		'&auml;'=>'htmlentity_small_a_dieresis_or_umlaut_mark',
		'&aring;'=>'htmlentity_small_a_ring',
		'&aelig;'=>'htmlentity_small_ae_diphthong_ligature',
		'&ccedil;'=>'htmlentity_small_c_cedilla',
		'&egrave;'=>'htmlentity_small_e_grave_accent',
		'&eacute;'=>'htmlentity_small_e_acute_accent',
		'&ecirc;'=>'htmlentity_small_e_circumflex_accent',
		'&euml;'=>'htmlentity_small_e_dieresis_or_umlaut_mark',
		'&igrave;'=>'htmlentity_small_i_grave_accent',
		'&iacute;'=>'htmlentity_small_i_acute_accent',
		'&icirc;'=>'htmlentity_small_i_circumflex_accent',
		'&iuml;'=>'htmlentity_small_i_dieresis_or_umlaut_mark',
		'&eth;'=>'htmlentity_small_eth_Icelandic',
		'&ntilde;'=>'htmlentity_small_n_tilde',
		'&ograve;'=>'htmlentity_small_o_grave_accent',
		'&oacute;'=>'htmlentity_small_o_acute_accent',
		'&ocirc;'=>'htmlentity_small_o_circumflex_accent',
		'&otilde;'=>'htmlentity_small_o_tilde',
		'&ouml;'=>'htmlentity_small_o_dieresis_or_umlaut_mark',
		'&oslash;'=>'htmlentity_small_o_slash',
		'&ugrave;'=>'htmlentity_small_u_grave_accent',
		'&uacute;'=>'htmlentity_small_u_acute_accent',
		'&ucirc;'=>'htmlentity_small_u_circumflex_accent',
		'&uuml;'=>'htmlentity_small_u_dieresis_or_umlaut_mark',
		'&yacute;'=>'htmlentity_small_y_acute_accent',
		'&thorn;'=>'htmlentity_small_thorn_Icelandic',
		//'&yuml;'=>'htmlentity_small_y_dieresis_or_umlaut_mark',
		'&fnof;'=>'htmlentity_latin_small_f_with_hook',			
		'&OElig;'=>'htmlentity_latin_capital_ligature_oe',
		'&oelig;'=>'htmlentity_latin_small_ligature_oe',
		'&Scaron;'=>'htmlentity_latin_capital_letter_s_with_caron',
		'&scaron;'=>'htmlentity_latin_small_letter_s_with_caron',
		'&Yuml;'=>'htmlentity_latin_capital_letter_y_with_diaeresis');		
		
		//number of replacements to check
		$total_replacements = count($foreign_char_array)-1;
		//get the keys from the array
		$keys = array_keys($foreign_char_array);
		//get the values from the array
		$values = array_values($foreign_char_array);
	
		//do the replacement
		for($i=0;$i<=$total_replacements;$i++)
		{
		
			$k = $keys[$i];
			$v = $values[$i];
			
			$t = htmlentities($text, ENT_QUOTES);
			
			if($whichway=="encode"){
				$t = str_replace($k,$v,$t);
			}
			else{
				$t = str_replace($v,$k,$t);
			}
			$text = html_entity_decode($t, ENT_QUOTES);
		}	
			
	return $text;
}
?>