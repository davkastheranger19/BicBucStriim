import React from 'react';
import WithLocalization from './WithLocalization'
import WithAuthentication from './WithAuthentication'

class WithDefaults extends React.Component {

	constructor() {
		super()
	}

	render() {
		const { children } = this.props
		return(
			<WithLocalization>
				{(locs) => (
					<WithAuthentication>
						{(auth) => (
	        				children(locs,auth)
						)}
					</WithAuthentication>
		        )}
		    </WithLocalization>	
		    )
	}

}

export default WithDefaults
