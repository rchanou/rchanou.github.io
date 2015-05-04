var koalaesce = require('koalaesce');

module.exports = {
  isCustomScoreboard: {
    label: 'Is Custom Scoreboard Slide',
    tip: () => 'If checked, the Speed Screen will treat this URL slide as a custom scoreboard, bringing it into view when races are running on the track it points to.',
    default: 0,
    type: 'boolean',
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  header: 'Header',
  line0: 'Header',
  line1: 'Line 1',
  line2: 'Line 2',
  line3: 'Line 3',
  line4: 'Line 4',
  line5: 'Line 5',
  line6: 'Line 6',
  html: 'HTML',
  original: 'URL',
  originalUrl: 'URL',

  startAtPosition: {
    label: 'Start At',
    default: 1,
    type: 'number'
  },

  url: {
    label: 'Full URL',
    tip: () => 'The full, direct URL to the page you want to use as a slide. It must include the protocol (e.g. "https://www.clubspeed.com").',
  },

  speedLevel: {
    label: 'Speed Level',
    default: 1,
    type: 'number'
  },

  showPicture: {
    label: 'Show Picture',
    default: 0,
    type: 'boolean',
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  showLineUpNumber: 'Show Lineup #', // deprecated, notice uppercase U

  showLineupNumber: {
    label: 'Show Lineup #',
    default: 0,
    type: 'boolean',
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  showKartNumber: {
    label: 'Show Kart #',
    default: 0,
    type: 'boolean',
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  rowsPerPage: {
    label: 'Rows Per Page',
    tip: () => 'The number of rows to show at a time.',
    default: 10,
    type: 'number'
  },

  duration: {
    label: 'Duration',
    tip: () => 'The number of seconds the slide will be shown before moving on to the next slide.',
    default: 15000, // TODO: check that this is the real default
    type: 'number',
    fieldWidth: 0.5,
    convertFromDb: val => 0.001 * val,
    convertToDb: val => 1000 * val
    //convertFromDb: val => (val >= 86400000)? 'Forever': 0.001 * val,
    //convertToDb:  val => (val >= 86400 || val.toUpperCase() === 'FOREVER')? 86400000: 1000 * val
  },

  postRaceIdleTime: {
    label: 'Post-Race Idle Time',
    tip: () => 'The number of seconds, after a race finishes, to leave the results on-screen before the normal lineup resumes.',
    default: 15000,
    type: 'number',
    fieldWidth: 0.5,
    convertFromDb: val => 0.001 * val,
    convertToDb: val => 1000 * val
  },

  track: {
    label: 'Track',
    tip: () => 'The track for which this slide will show stats.',
    default: 1,
    //type: 'number'
  },

  trackId: {
    label: 'Track',
    tip: slide => slide.typeId === 'raceUrl'? 'The track that this custom scoreboard slide points to, listening for new races.': 'The track for which this slide will show stats.',
    default: 1,
    convertFromDb: val => val? val: 1,
    convertToDb: val => val? val: 1,
    showForSlide: slide => slide.typeId !== 'raceUrl' || koalaesce.get(slide, 'options', 'isCustomScoreboard')
    //type: 'number'
  },

  theme: {
    label: 'Theme',
    tip: () => 'The theme of the scoreboard. "Classic" shows more stats, while "Big" shows fewer stats with a larger font size.',
    default: 'classic'
  },

  showConditions: '', // has dedicated tab
  eventSlides: '', // has dedicated tab, for scoreboards only as of 3/20/15

  backgroundUrl: {
    label: 'Background',
    tip: () => 'The background image for the Classic theme. If no image has been uploaded, the default will be used.',
    default: null,  // TODO: check that this is the real default
    convertFromDb: val => {
      return val;
      var res = decodeURIComponent(val);
      return res;
    },
    convertToDb: val => {
      return val;
      var res = encodeURIComponent(val);
      return res;
    },
  },

  range: {
    label: 'Range',
    tip: () => 'The time period from which the shown top times will be chosen.',
    default: 'day'
  },

  year: {
    label: 'Year',
    type: 'number',
    default: new Date().getFullYear()
  },

  offset: {
    label: 'Offset',
    tip: () => 'If set, the slide will show the stats for the race that occurred this many races ago.',
    type: 'number',
    default: 0
  },

  gender: {
    label: 'Gender',
    default: ''
  },

  pollingInterval: {
    label: 'Polling Interval',
    tip: () => 'The interval, <strong>in milliseconds</strong>, at which the scoreboard polls the Club Speed API for updates. Minimum is 500 ms.',
    default: 1000,
    type: 'number',
    min: 500,
    fieldWidth: 0.5,
    convertToDb: val => Math.max(500, val)
  },

  headerEnabled: {
    label: 'Enable Header',
    tip: () => 'Applies to the Big theme only. If enabled, the header will be visible.',
    default: 1,
    type: 'boolean',
    fieldWidth: 0.5,
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  showHeatNumber: {
    label: 'Show Heat #',
    tip: () => 'If enabled, the heat number for the race will be shown.',
    default: 1,
    type: 'boolean',
    fieldWidth: 0.5,
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  showHeatTime: {
    label: 'Show Heat Time',
    tip: () => 'If enabled, the time the race is scheduled to start will be shown.',
    default: 0,
    type: 'boolean',
    fieldWidth: 0.5,
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  showHeaderTimer: {
    label: 'Show Timer',
    tip: () => 'If enabled, the remaining race time will be shown.',
    default: 0,
    type: 'boolean',
    fieldWidth: 0.5,
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  locale: {
    label: 'Locale',
    tip: () => 'This determines the language and formatting of the slide.',
    default: 'en-US',
    fieldWidth: 0.5
  },

  highlightFastestRacer: {
    label: 'Highlight Fastest Racer',
    tip: () => 'If enabled, the racer\'s best lap time will change color if they are the fastest racer.',
    default: 1,
    type: 'boolean',
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0,
    fieldWidth: 0.5
  },

  fastestRacerColor: { // TODO: use a badass color picker for this!
    label: 'Fastest Racer Color',
    tip: () => 'Must be a hex color WITHOUT "#", e.g. "00FF00". If "Highlight Fastest Racer" is enabled, the fastest racer will be highlighted this color.',
    default: '00FF00',
    pattern: '^[0-9a-fA-F]{3,6}$',
    type: 'color'
  },

  racersPerPage: {
    label: 'Racers Per Page',
    tip: () => 'How many racers to show per page before paginating.',
    type: 'number',
    default: 10
  },

  timePerPage: {
    label: 'Seconds Per Page',
    tip: () => 'If paginated, the number of seconds to show each page.',
    default: 10000,
    type: 'number',
    fieldWidth: 0.5,
    convertFromDb: val => val * 0.001,
    convertToDb: val => val * 1000
  },

  nextRacerTabEnabled: {
    label: '"Next Racer" Tab Enabled',
    tip: () => 'Applies to the Classic theme only. If enabled, the "Next Racer" tab is shown.',
    default: 1,
    type: 'boolean',
    fieldWidth: 0.5,
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  filterRacers: {
    label: 'Filter Racers',
    tip: () => 'A number range, e.g. "6-10". If a valid range is set, only racers in these positions will be shown.',
    default: null,
    fieldWidth: 0.5,
    pattern: '^[0-9]*-[0-9]*$'
    //convertFromDb: val => val === 'off'? '': val,
    //convertToDb: val => !val? 'off': val
  },

  finalResultsTime: {
    label: 'Final Results Time',
    tip: () => 'Number of seconds to show final results after a race, before showing the next race.',
    default: 15000,
    type: 'number',
    fieldWidth: 0.5,
    convertFromDb: val => val * 0.001,
    convertToDb: val => val * 1000
  },

  showSequenceNumber: {
    label: 'Show Short Heat #',
    tip: () => 'If enabled, shows shortened heat numbers instead of full heat numbers.',
    default: 1,
    type: 'boolean',
    fieldWidth: 0.5,
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  showLapEstimation: {
    label: 'Show Lap Estimate',
    tip: () => 'If enabled, shows a translucent overlay for each racer estimating how soon they will complete a lap.',
    default: 0,
    type: 'boolean',
    fieldWidth: 0.5,
    convertFromDb: val => val? true: false,
    convertToDb: val => val? 1: 0
  },

  limit: {
    label: 'Limit',
    tip: () => 'The maximum number of racers that will be shown.',
    default: 10,
    type: 'number'
  },

  textLabelsColor: {
    label: 'Text Label Color',
    tip: () => 'Must be a hex color WITHOUT "#", e.g. "00FF00". Determines the color of text labels in the scoreboard, e.g. "Top Times", "Pos", "Best Lap".',
    default: 'FFFFFF',
    type: 'color'
  },

  textDataColor: {
    label: 'Text Data Color',
    tip: () => 'Must be a hex color WITHOUT "#", e.g. "00FF00". Determines the color of live timing data in the scoreboard, e.g. "29.21" (last lap time), "13" (kart number), "1" (position), "2L" (gap).',
    default: 'FFD700',
    type: 'color'
  },
};
