// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

contract WalletLedger {
    address public owner;

    mapping(address => uint256) private balances;
    mapping(address => uint256) private holds;

    event Funded(address indexed user, uint256 amount);
    event Withdrawn(address indexed user, uint256 amount, address to);
    event Held(address indexed user, uint256 amount);
    event HoldReleased(address indexed user, uint256 amount);

    modifier onlyOwner() {
        require(msg.sender == owner, "Not owner");
        _;
    }

    constructor() {
        owner = msg.sender;
    }

    function fundUser(address user) external payable onlyOwner {
        balances[user] += msg.value;
        emit Funded(user, msg.value);
    }

    function withdrawUser(address user, uint256 amount, address payable to) external onlyOwner {
        require(balances[user] >= amount, "Insufficient balance");
        require(balances[user] - holds[user] >= amount, "Funds held");
        balances[user] -= amount;
        to.transfer(amount);
        emit Withdrawn(user, amount, to);
    }

    function withdraw(uint256 amount, address payable to) external {
        require(balances[msg.sender] >= amount, "Insufficient balance");
        require(balances[msg.sender] - holds[msg.sender] >= amount, "Funds held");
        balances[msg.sender] -= amount;
        to.transfer(amount);
        emit Withdrawn(msg.sender, amount, to);
    }

    function holdUser(address user, uint256 amount) external onlyOwner {
        require(balances[user] - holds[user] >= amount, "Insufficient available");
        holds[user] += amount;
        emit Held(user, amount);
    }

    function releaseHold(address user, uint256 amount) external onlyOwner {
        require(holds[user] >= amount, "No hold to release");
        holds[user] -= amount;
        emit HoldReleased(user, amount);
    }

    function getBalance(address user) external view returns (uint256) {
        return balances[user];
    }

    function getHold(address user) external view returns (uint256) {
        return holds[user];
    }

    function getAvailableBalance(address user) external view returns (uint256) {
        return balances[user] - holds[user];
    }
}
