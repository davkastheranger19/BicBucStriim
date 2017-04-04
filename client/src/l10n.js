import LocalizedStrings from 'react-localization'

// TODO load language files dynamically, ca. 20KB!
import msgs_en from './data/l10n_en.json';
import msgs_de from './data/l10n_de.json';
import msgs_es from './data/l10n_es.json';
import msgs_fr from './data/l10n_fr.json';
import msgs_gl from './data/l10n_gl.json';
import msgs_hu from './data/l10n_hu.json';
import msgs_it from './data/l10n_it.json';
import msgs_nl from './data/l10n_nl.json';

export function Locs() { 
	return new LocalizedStrings({
	      en: msgs_en,
	      de: msgs_de,
	      es: msgs_es,
	      fr: msgs_fr,
	      gl: msgs_gl,
	      hu: msgs_hu,
	      it: msgs_it,
	      nl: msgs_nl
	})		
}
