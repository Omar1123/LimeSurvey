<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');
/*
 * LimeSurvey
 * Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v2 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 */

/**
 * Class SurveyLanguageSetting
 * @property string $title
 * @property string $description
 * @property string $welcometext
 * @property string $endtext
 * @property string $url
 * @property string $urldescription
 * @property string $language
 * @property int $dateformat
 * @property int $numberformat
 */
class SurveyLanguageSetting extends ActiveRecord
{
    /**
     * Add support for ommitting ugly surveyls prefix.
     * @param string $name
     */
    public function __get($name) {
        if ($this->hasAttribute('surveyls_' . $name)) {
            return parent::__get('surveyls_' . $name);
        } else {
            return parent::__get($name);
        }
    }
    /**
     * Add support for ommitting ugly surveyls prefix.
     * @param string $name
     */
    public function __set($name, $value) {
        if ($this->hasAttribute('surveyls_' . $name)) {
            parent::__set('surveyls_' . $name, $value);
        } else {
            parent::__set($name, $value);
        }

    }
    public function attributeLabels()
    {
        return [
            'surveyls_title' => gT("Survey title"),
            'surveyls_endtext' => gT("End message"),
            'surveyls_welcometext' => gT('Welcome message'),
            'surveyls_description' => gT('Description'),
            'surveyls_url' => gT("End URL"),
            'surveyls_urldescription' => gT('URL description'),
            'surveyls_dateformat' => gT('Date format'),
            'surveyls_numberformat' => gT('Decimal mark'),

        ];
    }

    /**
     * Returns the table's name
     *
     * @access public
     * @return string
     */
    public function tableName()
    {
        return '{{surveys_languagesettings}}';
    }

    /**
     * Returns the relations of this model
     *
     * @access public
     * @return array
     */
    public function relations()
    {
        $alias = $this->getTableAlias();
        return [
            'survey' => [self::BELONGS_TO, Survey::class, 'surveyls_survey_id'],
            'owner' => [self::BELONGS_TO, 'User', '', 'on' => 'survey.owner_id = owner.uid'],
        ];
    }


    /**
    * Returns this model's validation rules
    *
    */
    public function rules()
    {
        return [
            ['surveyls_email_invite_subj','lsdefault'],
            ['surveyls_email_invite','lsdefault'],
            ['surveyls_email_remind_subj','lsdefault'],
            ['surveyls_email_remind','lsdefault'],
            ['surveyls_email_confirm_subj','lsdefault'],
            ['surveyls_email_confirm','lsdefault'],
            ['surveyls_email_register_subj','lsdefault'],
            ['surveyls_email_register','lsdefault'],
            ['email_admin_notification_subj','lsdefault'],
            ['email_admin_notification','lsdefault'],
            ['email_admin_responses_subj','lsdefault'],
            ['email_admin_responses','lsdefault'],

            ['surveyls_email_invite_subj','LSYii_Validators'],
            ['surveyls_email_invite','LSYii_Validators'],
            ['surveyls_email_remind_subj','LSYii_Validators'],
            ['surveyls_email_remind','LSYii_Validators'],
            ['surveyls_email_confirm_subj','LSYii_Validators'],
            ['surveyls_email_confirm','LSYii_Validators'],
            ['surveyls_email_register_subj','LSYii_Validators'],
            ['surveyls_email_register','LSYii_Validators'],
            ['email_admin_notification_subj','LSYii_Validators'],
            ['email_admin_notification','LSYii_Validators'],
            ['email_admin_responses_subj','LSYii_Validators'],
            ['email_admin_responses','LSYii_Validators'],

            ['surveyls_title','LSYii_Validators'],
            ['surveyls_description','LSYii_Validators'],
            ['surveyls_welcometext','LSYii_Validators'],
            ['surveyls_endtext','LSYii_Validators'],
            ['surveyls_url','LSYii_Validators','isUrl'=>true],
            ['surveyls_urldescription','LSYii_Validators'],

            ['surveyls_dateformat', 'numerical', 'integerOnly'=>true, 'min'=>'1', 'max'=>'12', 'allowEmpty'=>true],
            ['surveyls_numberformat', 'numerical', 'integerOnly'=>true, 'min'=>'0', 'max'=>'1', 'allowEmpty'=>true],
        ];
    }


    /**
    * Defines the customs validation rule lsdefault
    *
    * @param mixed $attribute
    * @param mixed $params
    */
    public function lsdefault($attribute,$params)
    {
        if (isset($this->surveyls_survey_id)) {
            $oSurvey = $this->survey;
            $sEmailFormat = $oSurvey->htmlemail == 'Y' ? 'html' : '';
            $aDefaultTexts = templateDefaultTexts($this->surveyls_language, 'unescaped', $sEmailFormat);

            $aDefaultTextData = [
                'surveyls_email_invite_subj' => $aDefaultTexts['invitation_subject'],
                'surveyls_email_invite' => $aDefaultTexts['invitation'],
                'surveyls_email_remind_subj' => $aDefaultTexts['reminder_subject'],
                'surveyls_email_remind' => $aDefaultTexts['reminder'],
                'surveyls_email_confirm_subj' => $aDefaultTexts['confirmation_subject'],
                'surveyls_email_confirm' => $aDefaultTexts['confirmation'],
                'surveyls_email_register_subj' => $aDefaultTexts['registration_subject'],
                'surveyls_email_register' => $aDefaultTexts['registration'],
                'email_admin_notification_subj' => $aDefaultTexts['admin_notification_subject'],
                'email_admin_notification' => $aDefaultTexts['admin_notification'],
                'email_admin_responses_subj' => $aDefaultTexts['admin_detailed_notification_subject'],
                'email_admin_responses' => $aDefaultTexts['admin_detailed_notification']
            ];
            if ($sEmailFormat == "html") {
                $aDefaultTextData['admin_detailed_notification'] = $aDefaultTexts['admin_detailed_notification_css'] . $aDefaultTexts['admin_detailed_notification'];
            }

            if (empty($this->$attribute)) {
                $this->$attribute = $aDefaultTextData[$attribute];
            }
        }
    }


    public function getDateFormatOptions() {
        return array_map(function($e) { return $e['dateformat']; }, \ls\helpers\SurveyTranslator::getDateFormatData());
    }
    public function getNumberFormatOptions() {
        return array_map(function($e) { return $e['desc']; }, \ls\helpers\SurveyTranslator::getRadixPointData());
    }
}
