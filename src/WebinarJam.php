<?php
namespace Awolacademy\Webinarjam;

use Exception;
use Log;

class WebinarJam
{
    protected $jandje;

    public function __construct(BaseJandjeJam $jandje)
    {
        $this->jandje = $jandje;
    }

    /**
     * Get all webinars on your account
     * @return string
     * @throws WebinarJamException
     */
    public function getAllWebinars()
    {
        $endpoint = 'webinars';
        $response = $this->callApi('getallwebinars', $endpoint);
        return $response;
    }

    /**
     * Get webinar by ID on your account
     * @param $webinarId
     * @throws WebinarJamException
     */
    public function getWebinar($webinarId)
    {
        $endpoint = 'webinar';
        $data = array('webinar_id' => $webinarId);
        $response = $this->callApi('getwebinar', $endpoint, $data);
        return $response;
    }

    /**
     * Register the person to a webinar
     * @param $webinarId
     * @param $name
     * @param $email
     * @param $schedule
     * @param $timezone
     * @return string
     * @throws WebinarJamException
     */
    public function registerToWebinar($webinarId, $name, $email, $schedule, $timezone = null)
    {
        $endpoint = 'register';

        $data = [];
        if(!empty($webinarId)) {
            $data['webinar_id'] = $webinarId;
        }
        if(!empty($name)) {
            if ($this->jandje instanceof JandjeGenndiEverWebinarJam) {
                $parts = explode(' ', $name);
                if (count($parts) > 1) {
                    $name_last = array_pop($parts);
                    $name_first = trim(implode(' ', $parts));
                    $data['first_name'] = $name_first;
                    $data['last_name'] = $name_last;
                } else {
                    $data['first_name'] = $name;
                }
            } else {
                $data['name'] = $name;
            }
        }
        if(!empty($email)) {
            $data['email'] = $email;
        }
        if(ctype_digit($schedule)) {
            $data['schedule'] = $schedule;
        }
        if ($this->jandje instanceof JandjeGenndiEverWebinarJam) {
            if(!empty($timezone)) {
                $data['timezone'] = $timezone;
            }
        }

        $response = $this->callApi('registertowebinar', $endpoint, $data);
        return $response;
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $data = []
     * @return array $response
     * @throws MailchimpException
     */
    protected function callApi($method, $endpoint, $data = [])
    {
        try {
            $response = $this->jandje->$method($endpoint, $data);
        } catch (Exception $e) {
            throw new WebinarJamException('JandjeWebinarJam exception: ' . $e->getMessage());
        }
        if ($response === false) {
            throw new WebinarJamException('Error in JandjeWebinarJam - possible connectivity problem');
        }
        return $response;
    }
}