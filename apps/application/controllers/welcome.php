<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Welcome extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        if ($_POST) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean');
            $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                return false;
            } else {
                $name = $this->input->post('name');
                $email = $this->input->post('email');
                $phone = $this->input->post('phone');
                $message = $this->input->post('message');
                $message .= "\n\n" .
                    "-------------------\n" .
                    "Sender information:\n" .
                    "Name: $name\n" .
                    "Phone: $phone";
                $receipt_message = '' .
                    'Dear ' . $name . "\n" .
                    "\n" .
                    'Thank you for your email, we will proceed your request ASAP.' . "\n" .
                    "\n" .
                    'Regards,' . "\n" .
                    'AK Experience Team';

                $this->load->library('email');

                $this->email->from('bot@akxperience.com', 'AK Experience');
                $this->email->reply_to($email, $name);
                $this->email->to('ak.experience@auto-kencana.com');
                $this->email->bcc('faisal.murkadi@jayadata.co.id');
                $this->email->bcc('ari.cahyo@auto-kencana.com');

                $this->email->subject('AK Experience Contact Form');
                $this->email->message($message);

                $this->email->send();

                $this->email->from('no-reply@akxperience.com', 'Auto Kencana No-Reply');
                $this->email->to($email);

                $this->email->subject('AK Experience Contact Form');
                $this->email->message($receipt_message);

                $this->email->send();

                return true;
            }
        }

        $this->load->view('homepage');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */