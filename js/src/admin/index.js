import app from 'flarum/admin/app';

app.initializers.add('tapao/custom-landing-page', () => {
  app.extensionData
    .for('tapao-custom-landing-page')
    .registerSetting({
      setting: 'tapao-custom-landing-page.enabled',
      type: 'boolean',
      label: app.translator.trans('tapao-custom-landing-page.admin.settings.enabled_label'),
    })
    .registerSetting({
      setting: 'tapao-custom-landing-page.guests_only',
      type: 'boolean',
      label: app.translator.trans('tapao-custom-landing-page.admin.settings.guests_only_label'),
      help: app.translator.trans('tapao-custom-landing-page.admin.settings.guests_only_help'),
    })
    .registerSetting({
      setting: 'tapao-custom-landing-page.html',
      type: 'textarea',
      label: app.translator.trans('tapao-custom-landing-page.admin.settings.html_label'),
      help: app.translator.trans('tapao-custom-landing-page.admin.settings.html_help'),
    });
});
