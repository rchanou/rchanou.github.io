var SLIDE_PATH = '/api/slides/';

module.exports = {
  eventScreen: {
    label: 'Event Screen',
    optionSet: ['duration', 'trackId'],
    disabled: true
  },

  image: {
    label: 'Image',
    optionSet: ['duration', 'url']
  },

  lastWinnerwithPicture: { // deprecated due to incorrect casing, left in for compatibility
    label: 'Last Winner with Picture',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'last-winner.html',
    optionSet: ['duration', 'track', 'backgroundUrl', 'offset'],
    disabled: true
  },

  lastWinnerWithPicture: {
    label: 'Last Winner with Picture',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'last-winner.html',
    optionSet: ['duration', 'backgroundUrl', 'track', 'offset'],
  },

  mostImprovedRPMOfMonth: {
    disabled: true,
    label: 'Most Improved ProSkill of the Month',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'most-improved-proskill.html',
    optionSet: ['duration', 'backgroundUrl', 'startAtPosition', 'limit'],
    fixedOptions: { range: 'month' }
  },

  mostImprovedRPMOfYear: {
    disabled: true,
    label: 'Most Improved ProSkill of the Year',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'most-improved-proskill.html',
    optionSet: ['duration', 'backgroundUrl', 'startAtPosition', 'limit', 'year'],
    fixedOptions: { range: 'year' }
  },

  mostImprovedProSkillOfMonth: {
    label: 'Most Improved ProSkill of the Month',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'most-improved-proskill.html',
    optionSet: ['duration', 'backgroundUrl', 'limit'],
    fixedOptions: { range: 'month' }
  },

  mostImprovedProSkillOfYear: {
    label: 'Most Improved ProSkill of the Year',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'most-improved-proskill.html',
    optionSet: ['duration', 'backgroundUrl', 'limit', 'year'],
    fixedOptions: { range: 'year' }
  },

  nextRacers: {
    label: 'Next Racers',
    getBaseUrl: slide => config.origin.replace('https', 'http') + SLIDE_PATH + 'up-next' + (slide.options.showPicture? '-pictures': '') + '.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl', 'showPicture']
  },

  nextNextRacers: {
    label: 'Next Next Racers',
    getBaseUrl: slide => config.origin.replace('https', 'http') + SLIDE_PATH + 'up-next'
                + (slide.options.showPicture? '-pictures': '') + '.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl', 'showPicture'],
    fixedOptions: { offset: 1 }
  },

  previousPreviousRaceResults: {
    label: 'Prev. Prev. Race Results',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'previous.html',
    optionSet: ['duration', 'track', 'backgroundUrl'],
    fixedOptions: { offset: 1 }
  },

  previousRaceResults: {
    label: 'Previous Race Results',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'previous.html',
    optionSet: ['duration', 'track', 'backgroundUrl'],
    fixedOptions: { offset: 0 }
  },

  schedule: {
    label: 'Schedule',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'schedule.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl']
  },

  topProskill: {
    label: 'Top ProSkill',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'top-proskill.html',
    optionSet: ['duration', 'backgroundUrl', 'limit', 'gender'],
    fixedOptions: { range: 'month' }
  },

  topTime: {
    label: 'Top Time',
    getBaseUrl: slide => config.origin.replace('https', 'http') + SLIDE_PATH + 'top-times' + (slide.options.showPicture? '-pictures': '') + '.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl', 'range', 'limit', 'showPicture']
  },

  topTimeOfDay: {
    label: 'Top Time of Day',
    getBaseUrl: slide => config.origin.replace('https', 'http') + SLIDE_PATH + 'top-times' + (slide.options.showPicture? '-pictures': '') + '.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl', 'limit', 'showPicture'],
    fixedOptions: { range: 'day' },
    disabled: true
  },

  topTimeOfDayWithPicture: {
    label: 'Top Time of Day with Picture',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'top-times-pictures.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl', 'limit'],
    fixedOptions: { range: 'day' },
    disabled: true
  },

  topTimeOfMonth: {
    label: 'Top Time of Month',
    getBaseUrl: slide => config.origin.replace('https', 'http') + SLIDE_PATH + 'top-times' + (slide.options.showPicture? '-pictures': '') + '.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl', 'limit', 'showPicture'],
    fixedOptions: { range: 'month' },
    disabled: true
  },

  topTimeOfYear: {
    label: 'Top Time of Year',
    getBaseUrl: slide => config.origin.replace('https', 'http') + SLIDE_PATH + 'top-times' + (slide.options.showPicture? '-pictures': '') + '.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl', 'limit', 'showPicture'],
    fixedOptions: { range: 'year' },
    disabled: true
  },

  topTimeOfWeek: {
    label: 'Top Time of Week',
    getBaseUrl: slide => config.origin.replace('https', 'http') + SLIDE_PATH + 'top-times' + (slide.options.showPicture? '-pictures': '') + '.html',
    optionSet: ['duration', 'trackId', 'backgroundUrl', 'limit', 'showPicture'],
    fixedOptions: { range: 'week' },
    disabled: true
  },

  text: {
    label: 'Text',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'text.html',
    optionSet: ['duration', 'backgroundUrl', 'line0', 'line1', 'line2', 'line3', 'line4', 'line5', 'line6']
  },

  video: {
    label: 'Video',
    optionSet: ['duration', 'originalUrl'],
    disabled: true
  },

  url: {
    label: 'Direct URL',
    optionSet: ['duration', 'url']
  },

  raceUrl: {
    label: 'Direct URL',
    optionSet: ['duration', 'url', 'isCustomScoreboard', 'trackId']
  },

  html: {
    label: '(NOT FUNCTIONAL) HTML',
    getBaseUrl: () => config.origin.replace('https', 'http') + SLIDE_PATH + 'text.html',
    optionSet: ['duration', 'html'],
    disabled: true
  },

  scoreboard: {
    label: 'Scoreboard',
    getBaseUrl: slide => {
      if (config.origin.replace('https', 'http').indexOf('vm-122') > -1){
        var path = 'http://192.168.111.201/scoreboard/#/';
      } else {
        var path = '/cs-speedscreen/pages/slides/scoreboard/#/';
      }
      return path + (slide.options.trackId || '1') + '/' + (slide.options.theme || 'classic') + '/'
    },
    optionSet: [
      'postRaceIdleTime',
      'trackId',
      'backgroundUrl',
      'theme',
      'pollingInterval',
      'headerEnabled',
      'showHeatNumber',
      'showHeatTime',
      'showHeaderTimer',
      'locale',
      'highlightFastestRacer',
      'fastestRacerColor',
      'textLabelsColor',
      'textDataColor',
      'racersPerPage',
      'timePerPage',
      'nextRacerTabEnabled',
      'filterRacers',
      'finalResultsTime',
      'showSequenceNumber',
      'showLapEstimation'
    ]
  }
};
