import React from 'react';
import {PageHeader} from 'react-bootstrap'

import WithDefaults from './WithDefaults'
import TilesGrid from './TitlesGrid'

const tilesData = [
  {
    id: 2,
    img: 'titles/thumb_2.png',
    title: 'Hoffnun\'1',
    author: 'Ansari, Puneh',
  },
  {
    id: 3,
    img: 'titles/thumb_3.png',
    title: 'Science Fiction Jahr 2016, Das eloquent und schöne',
    author: 'Riffel, Hannes',
  },
  {
    id: 4,
    img: 'titles/thumb_4.png',
    title: 'Camera',
    author: 'Danson67',
  },
  {
    id: 5,
    img: 'titles/thumb_5.png',
    title: 'Morning',
    author: 'fancycrave1',
  },
  {
    id: 6,
    img: 'titles/thumb_6.png',
    title: 'Hats',
    author: 'Hans',
  },
  {
    id: 7,
    img: 'titles/thumb_7.png',
    title: 'Honey',
    author: 'fancycravel',
  },
  {
    id: 8,
    img: 'titles/thumb_8.png',
    title: 'Vegetables',
    author: 'jill111',
  },
  {
    id: 537,
    img: 'titles/thumb_537.png',
    title: 'Water plant',
    author: 'BkrmadtyaKarki',
  }
];


class Home extends React.Component {

	render() {
		return (
      <WithDefaults>
        {(locs) => (
          <div>
    				<PageHeader>{locs.dl30}</PageHeader>
            <TilesGrid titleData={tilesData} />
          </div>
        )}
      </WithDefaults>
		)
	}
	
}

export default Home;
