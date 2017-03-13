import React from 'react';
import {Locs} from '../l10n'

class WithLocalization extends React.Component {

	constructor() {
		super()
		// TODO Loc Daten in state laden
		this.state = {localization: Locs()}
	}

	render() {
		const { localization } = this.state
    	const { children } = this.props

	    return (localization != null ? children(localization) : null)	  	
	}
}

export default WithLocalization
