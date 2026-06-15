<?php defined('BASEPATH') or exit('No direct script access allowed');

trait ForgotPasswordTrait
{
    //--------------------------------------------------------------
    // UPDATED: Display the forgot-password page and email a secure,
    // time-limited reset link when a registered email is submitted.
    public function forget_password()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('auth/forget-password'), 'refresh');
            }

            $email = trim($this->input->post('email', TRUE));
            $user = $this->auth_model->check_user_mail($email);

            // UPDATED: Always show the same response so registered email
            // addresses cannot be discovered from this public form.
            if ($user) {
                $reset_code = bin2hex(random_bytes(32));
                $reset_created_at = date('Y-m-d H:i:s');

                if ($this->auth_model->update_reset_code($reset_code, $user['admin_id'], $reset_created_at)) {
                    $reset_link = base_url('auth/reset-password/' . $reset_code);
                    $this->_send_password_reset_email($user, $reset_link);
                }
            }

            $this->session->set_flashdata(
                'success',
                'If the email address is registered, a password reset link has been sent.'
            );
            redirect(base_url('auth/forget-password'), 'refresh');
        }

        $data['title'] = 'Forget Password';
        $data['navbar'] = false;
        $data['sidebar'] = false;
        $data['footer'] = false;
        $data['bg_cover'] = true;

        $this->load->view('includes/_header', $data);
        $this->load->view('auth/forget_password');
        $this->load->view('includes/_footer', $data);
    }

    //--------------------------------------------------------------
    // UPDATED: Validate the reset token, enforce strong matching passwords,
    // update the existing ci_admin password hash, then invalidate the token.
    public function reset_password($reset_code = '')
    {
        $reset_code = trim($reset_code);
        $user = $this->auth_model->get_user_by_valid_reset_code($reset_code, 60);

        if (!$user) {
            $this->session->set_flashdata('error', 'This password reset link is invalid or has expired.');
            redirect(base_url('auth/forget-password'), 'refresh');
        }

        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules(
                'password',
                'New Password',
                'trim|required|min_length[8]|callback_reset_password_strength_check'
            );
            $this->form_validation->set_rules(
                'confirm_password',
                'Confirm Password',
                'trim|required|matches[password]'
            );

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('auth/reset-password/' . $reset_code), 'refresh');
            }

            $new_password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);

            if ($this->auth_model->reset_password($reset_code, $new_password)) {
                $this->session->set_flashdata('success', 'Your password has been reset successfully. Please login.');
                redirect(base_url('auth/login'), 'refresh');
            }

            $this->session->set_flashdata('error', 'Password could not be reset. Please try again.');
            redirect(base_url('auth/reset-password/' . $reset_code), 'refresh');
        }

        $data['title'] = 'Reset Password';
        $data['navbar'] = false;
        $data['sidebar'] = false;
        $data['footer'] = false;
        $data['bg_cover'] = true;
        $data['reset_code'] = $reset_code;

        $this->load->view('includes/_header', $data);
        $this->load->view('auth/reset_password', $data);
        $this->load->view('includes/_footer', $data);
    }

    //--------------------------------------------------------------
    // UPDATED: Strong password callback used only by reset-password.
    public function reset_password_strength_check($password)
    {
        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
            return true;
        }

        $this->form_validation->set_message(
            'reset_password_strength_check',
            'Password must contain at least 8 characters, including uppercase, lowercase, number and special character.'
        );
        return false;
    }

    //--------------------------------------------------------------
    // UPDATED: Send reset mail with the SMTP values already maintained
    // in General Settings. No new configuration or table is introduced.
    private function _send_password_reset_email($user, $reset_link)
    {
        $this->load->library('email');

        $smtp_port = !empty($this->general_settings['smtp_port'])
            ? (int) $this->general_settings['smtp_port']
            : 465;

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => $this->general_settings['smtp_host'],
            'smtp_port' => $smtp_port,
            'smtp_user' => $this->general_settings['smtp_user'],
            'smtp_pass' => $this->general_settings['smtp_pass'],
            'smtp_crypto' => ($smtp_port === 465) ? 'ssl' : 'tls',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
            'crlf' => "\r\n"
        );

        $this->email->initialize($config);

        $application_name = !empty($this->general_settings['application_name'])
            ? $this->general_settings['application_name']
            : 'RemodelWell';
        $from_email = !empty($this->general_settings['email_from'])
            ? $this->general_settings['email_from']
            : $this->general_settings['smtp_user'];

        $message = '<p>Hello ' . html_escape($user['name']) . ',</p>'
            . '<p>We received a request to reset your ' . html_escape($application_name) . ' password.</p>'
            . '<p><a href="' . html_escape($reset_link) . '">Click here to reset your password</a></p>'
            . '<p>This link will expire in 60 minutes.</p>'
            . '<p>If you did not request this change, you can ignore this email.</p>';

        $this->email->from($from_email, $application_name);
        $this->email->to($user['email']);
        $this->email->subject('Reset Your Password - ' . $application_name);
        $this->email->message($message);

        return $this->email->send();
    }
}
