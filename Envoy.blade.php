@servers(['web-1' => ['ivxch-node1'], 'web-2' => ['ivxch-node2']])

@setup
    $site = ('ucastx' == $app) ? 'ivxtest.ivideocloud.cn' : 'cnctest.ivideocloud.cn';
    $repository = 'git@gitlab.com:ucast-tv/content-exchange/IVX-China.git';
    $releases_dir = '/var/www/html/'.$site.'/releases';
    $app_dir = '/var/www/html/'.$site;
    $code_base = $app_dir.'/code_base/IVX-China';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
    $node_modules_dir = '/var/www/html/'.$site.'/node-modules';
    $supervisorctl_cmd = '/usr/bin/supervisorctl';
    $supervisorctl_start = '/usr/bin/supervisorctl start all';
    $supervisorctl_stop = '/usr/bin/supervisorctl stop all';
@endsetup

@story('deploy', ['on' => ['web-1', 'web-2'], 'parallel' => true])
    stop_supervisorctl
    git_pull
    run_composer
    copy_release
    run_npm
    update_symlinks
    run_migrate
    start_supervisorctl
    remove_repository
@endstory

@task('stop_supervisorctl')
    if [ -f {{ $supervisorctl_cmd }} ]
    then
    echo "Stopping supervisorctl"
    {{ $supervisorctl_stop }}
    fi
@endtask

@task('git_pull')
    echo 'Git pull'
    cd {{ $code_base }}
    git pull
@endtask

@task('run_composer')
    echo "Run Composer"
    cd {{ $code_base }}
    composer install --prefer-dist --no-scripts -q -o
    php artisan app:setup {{ $app }}
    php artisan vue-i18n:generate
@endtask

@task('copy_release')
    echo 'Copy release'
    [ -d {{ $releases_dir }} ] || mkdir {{ $releases_dir }}
    cp -R {{ $code_base }} {{ $new_release_dir }}
    chown -R root:nginx {{ $new_release_dir }}
    chmod 775 {{ $new_release_dir }}/bootstrap/cache
@endtask

@task('run_npm')
    echo "Starting run npm"
    cp {{ $new_release_dir }}/package.json {{ $node_modules_dir }}
    cp {{ $new_release_dir }}/package-lock.json {{ $node_modules_dir }}
    cd {{ $node_modules_dir }}
    npm install
    cp -r node_modules {{ $new_release_dir }}
    cd {{ $new_release_dir }}
    npm run dev
@endtask

@task('update_symlinks')
    echo "Linking storage directory"
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage

    echo 'Linking .env file'
    ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env

    echo 'Linking current release'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
@endtask

@task('start_supervisorctl')
    if [ -f {{ $supervisorctl_cmd }} ]
    then
    echo "Starting supervisorctl"
    {{ $supervisorctl_start }}
    fi
@endtask

@task('run_migrate')
    echo "Starting run migrate"
    cd {{ $app_dir }}/current
    php artisan migrate
@endtask

@task('remove_repository')
    echo 'Remove oldest repository'
    oldest=`ls {{ $releases_dir }} -t | tail -1`
    rm -rf {{ $releases_dir }}/$oldest
@endtask