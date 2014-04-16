<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"         => ":attribute 항목이 설정되지 않았습니다.",
	"active_url"       => ":attribute 항목이 유효한 URL이 아닙니다.",
	"after"            => ":attribute 항목은 :date보다 나중이어야 합니다.",
	"alpha"            => ":attribute 항목은 오직 알파벳만 허용됩니다.",
	"alpha_dash"       => ":attribute 항목은 오직 알파벳, 숫자, 그리고 대쉬 기호만 허용됩니다.",
	"alpha_num"        => ":attribute 항목은 오직 알파벳과 숫자만 허용됩니다.",
	"array"            => ":attribute 항목은 배열이어야 합니다.",
	"before"           => ":attribute 항목은 :date보다 이전이어야 합니다.",
	"between"          => array(
		"numeric" => ":attribute 항목의 값은 :min과 :max 사이이어야 합니다.",
		"file"    => ":attribute 파일의 크기는 :minKB 이상 :maxKB 이하이어야 합니다.",
		"string"  => ":attribute 항목의 길이는 :min글자 이상 :max글자 이하이어야 합니다.",
		"array"   => ":attribute 항목의 개수는 :min개 이상 :max개 이하이어야 합니다.",
	),
	"confirmed"        => ":attribute 항목은 확인란과 일치해야 합니다.",
	"date"             => ":attribute 항목이 유효한 날짜 형식이 아닙니다.",
	"date_format"      => ":attribute 항목이 :format 형식이 아닙니다.",
	"different"        => ":attribute 항목과 :other 항목은 서로 달라야 합니다.",
	"digits"           => ":attribute 항목은 :digits자리 수이어야 합니다.",
	"digits_between"   => ":attribute 항목은 :min자리 이상 :max자리 이하이어야 합니다.",
	"email"            => ":attribute 항목이 유효한 이메일 형식이 아닙니다.",
	"exists"           => ":attribute 항목이 유요한 값이 아닙니다.",
	"image"            => ":attribute 항목은 이미지이어야 합니다.",
	"in"               => ":attribute 항목이 유효하지 않습니다.",
	"integer"          => ":attribute 항목은 정수이어야 합니다.",
	"ip"               => ":attribute 항목이 유효한 IP 주소 형식이 아닙니다.",
	"max"              => array(
		"numeric" => ":attribute 항목의 최대값은 :max입니다.",
		"file"    => ":attribute 파일의 최대 크기는 :maxKB입니다.",
		"string"  => ":attribute 항목의 최대 길이는 :max글자입니다.",
		"array"   => ":attribute 항목의 원소는 최대 :max개까지 허용됩니다.",
	),
	"mimes"            => ":attribute 항목은 다음 파일형식 중 하나이어야 합니다: :values.",
	"min"              => array(
		"numeric" => ":attribute 항목의 최소값은 :min입니다.",
		"file"    => ":attribute 파일의 최소 크기는 :minKB입니다.",
		"string"  => ":attribute 항목의 최소 길이는 :min글자입니다.",
		"array"   => ":attribute 항목의 원소는 최소 :min개 이상이어야 합니다.",
	),
	"not_in"           => ":attribute 항목이 유효하지 않습니다.",
	"numeric"          => ":attribute 항목은 숫자이어야 합니다.",
	"regex"            => ":attribute 항목의 형식이 유효하지 않습니다.",
	"required"         => ":attribute 항목을 입력해주세요.",
	"required_if"      => ":other 항목의 값이 :value일 때, :attribute 항목을 입력해야 합니다.",
	"required_with"    => ":values 값이 있을 때, :attribute 항목을 입력해야 합니다.",
	"required_without" => ":values 값이 없을 때, :attribute 항목을 입력해야 합니다.",
	"same"             => ":attribute 항목과 :other 항목은 서로 일치해야 합니다.",
	"size"             => array(
		"numeric" => ":attribute 항목의 크기는 :size이어야 합니다.",
		"file"    => ":attribute 항목의 크기는 :sizeKB이어야 합니다.",
		"string"  => ":attribute 항목의 길이는 :size이어야 합니다.",
		"array"   => ":attribute 항목의 개수는 :size이어야 합니다.",
	),
	"unique"           => ":attribute 항목의 값이 이미 존재합니다.",
	"url"              => ":attribute 항목이 올바른 URL 형식이 아닙니다.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
