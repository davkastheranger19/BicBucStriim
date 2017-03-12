import React from 'react';
import {Locs} from '../l10n'

class WithAuthentication extends React.Component {

	constructor() {
		super()
		// TODO Loc Daten in state laden
		this.state = {auth: Locs()}
	}

	render() {
		const { auth } = this.state
    	const { children } = this.props

	    return (auth != null ? children(auth) : null)	  	
	}
}

export default WithAuthentication
