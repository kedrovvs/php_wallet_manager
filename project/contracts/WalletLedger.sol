// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

contract WalletLedger {
    address public owner;

    mapping(uint256 => uint256) private balances;
    mapping(uint256 => uint256) private holds;

    event Funded(uint256 indexed userId, uint256 amount);
    event Withdrawn(uint256 indexed userId, uint256 amount, address to);
    event Held(uint256 indexed userId, uint256 amount);
    event HoldReleased(uint256 indexed userId, uint256 amount);

    modifier onlyOwner() {
        require(msg.sender == owner, "Not owner");
        _;
    }

    constructor() {
        owner = msg.sender;
    }

    function fundUser(uint256 userId) external payable onlyOwner {
        balances[userId] += msg.value;
        emit Funded(userId, msg.value);
    }

    function withdrawUser(uint256 userId, uint256 amount, address payable to) external onlyOwner {
        require(balances[userId] >= amount, "Insufficient balance");
        require(balances[userId] - holds[userId] >= amount, "Funds held");
        balances[userId] -= amount;
        to.transfer(amount);
        emit Withdrawn(userId, amount, to);
    }

    function holdUser(uint256 userId, uint256 amount) external onlyOwner {
        require(balances[userId] - holds[userId] >= amount, "Insufficient available");
        holds[userId] += amount;
        emit Held(userId, amount);
    }

    function releaseHold(uint256 userId, uint256 amount) external onlyOwner {
        require(holds[userId] >= amount, "No hold to release");
        holds[userId] -= amount;
        emit HoldReleased(userId, amount);
    }

    function getBalance(uint256 userId) external view returns (uint256) {
        return balances[userId];
    }

    function getHold(uint256 userId) external view returns (uint256) {
        return holds[userId];
    }

    function getAvailableBalance(uint256 userId) external view returns (uint256) {
        return balances[userId] - holds[userId];
    }
}
