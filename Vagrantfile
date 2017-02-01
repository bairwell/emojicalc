listening_port = "8000"

Vagrant.configure("2") do |config|
  config.vm.box = "rasmus/php7dev"
  config.vm.box_version = "1.0.0"
  # disabiling automatic box updates can be risky, but disabling
  # them does make startups faster
  config.vm.box_check_update = false
  config.vm.network "forwarded_port", guest: 8000, host: listening_port
  # config.vm.network "private_network", ip: "192.168.7.12"
  # config.vm.network "public_network"
  # config.vm.synced_folder "../data", "/vagrant_data"

  # config.vm.provision "shell", inline: <<-SHELL
  #   apt-get update
  #   makephp 7
  #   apt-get install -y apache2
  # SHELL

  # stop nginx
  # start PHP's inbuilt webserver
  config.vm.provision "shell", run: "always", :args => [listening_port], inline: <<-SHELL
     service nginx stop
     nohup php -S 0.0.0.0:$1 -t /vagrant/public/ &
     echo "Ready - access http://localhost:$1/ to preview"
  SHELL

end
