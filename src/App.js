import React, {Component} from 'react';

import PollComponent from './PollComponent'
import CreatePoll from './CreatePoll'



class App extends Component {
  constructor(props) {
    super(props);

    this.state = {
      polls: JSON.parse(localStorage.getItem('polls')) || []
    };

    this.deletePoll = this.deletePoll.bind(this)
    this.addPoll = this.addPoll.bind(this)
    this.updatePoll = this.updatePoll.bind(this)
  }

  componentDidMount() {
    const polls = this.getPolls();

    this.setState(polls);
  }

  getPolls() {
    return JSON.parse(localStorage.getItem('polls'));
  }

  deletePoll(title) {
    const polls = this.getPolls();

    const filteredPolls = polls.filter(poll => {
      return poll.title !== title;
    });
    
    localStorage.setItem('polls', JSON.stringify(filteredPolls));
    this.setState({ polls: filteredPolls });
  }

  addPoll(title, opt1, opt2) {
    const polls = this.getPolls();
    
    polls.push({
      title,
      didVote: false,
      options: [
        {
          title: opt1,
          count: 0,
          votedFor: false,
        }, 
        {
          title: opt2,
          count: 0,
          votedFor: false,
        }
      ]
    })

    localStorage.setItem('polls', JSON.stringify(polls));
    this.setState({polls})
  }

  updatePoll(title, option) {
    const polls = this.getPolls();

    polls.forEach(poll => {
      if(poll.title === title){
        poll.options.forEach(pollChoice => {
          if(pollChoice.title !== option && pollChoice.votedFor && poll.didVote) {
            pollChoice.count --
            pollChoice.votedFor = false
          } else if(pollChoice.title === option && !pollChoice.votedFor) {
            pollChoice.count ++
            pollChoice.votedFor = true
          }
        })
        
      }
      poll.didVote = true
    })
    
    localStorage.setItem('polls', JSON.stringify(polls));
    this.setState({polls})
    
  }

  render() {

    return (
      <div className="App">
        <header className="App-header">
          <p>
            Polling Application
          </p>
        </header>
        <CreatePoll
          addPoll={this.addPoll}
        />
        <div>
          <h1>
            Current Polls
          </h1>
          { 
            this.state.polls.map(poll => {
              return (
                <PollComponent
                  key={poll.title}
                  {...poll}
                  deletePoll={this.deletePoll}
                  updatePoll={this.updatePoll}
                />
              )
            }
          )
          
          }
        </div>
      </div>
    );
  }
}


export default App;
